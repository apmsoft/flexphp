<?php
/* ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : https://www.fancyupsoft.com
| @Editor   : VSCode
| @version : 0.9.4
----------------------------------------------------------*/
namespace Flex\Util;

use Fus3\Util\UtilModel;
use Fus3\R\R;
use Fus3\Req\ReqForm;
use Fus3\Db\DbHelperWhere;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Db\DbMySqli;
use \ErrorException;

use PushSDK\Push\PushSend;

#config.json 컨트롤
class UtilConfig extends UtilConfigCompiler
{
    protected $manifest = array();

    public function __construct($doc_id,$config_id)
	{
        R::parserResource(
            _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_.DIRECTORY_SEPARATOR.$doc_id.DIRECTORY_SEPARATOR.$config_id.'.json', 
                'config'
        );

        if(is_array(R::$config)){
            foreach(R::$config as $propertyName => $arg){
                if(property_exists(__CLASS__,$propertyName)){
                    $this->{$propertyName} = $arg;
                }
            }
        }

        # compiler
        parent::__construct();

        # 권한체크
        if(property_exists(__CLASS__,'authority')){
            #echo "authority 1: ".$this->authority."\r\n";
            $this->authority = $this->compile($this->authority);
            #echo "authority 2: ".$this->authority."\r\n";
        }
    }

    # validation Check
    protected function doValidation()
    {
        #echo "validation========="."\r\n\r\n";
        if(is_array($this->validation))
        {
            #print_r($this->validation);

            // reqForm
            $reqForm = new ReqForm();

            foreach($this->validation as $column => $params)
            {
                if(!is_null($params['filter']))
                {
                    if(strpos($column, ',') !==false)
                    {
						$tmp_columns = array();
						$c_arr       = explode(',', $column);
						$count       = count($c_arr);
						$max_num     = $count - 1;
						foreach($c_arr as $cv){
							$tmp_columns[] = (strpos($cv,'{') !==false) ? $this->compile($cv): $this->request[$cv];
                        }
                        
						call_user_func_array(
                            array($reqForm,$params['filter']), 
                            array($c_arr[$max_num], $params['title'], $tmp_columns, $params['required'])
                        );
					}else{
						call_user_func_array(
                            array($reqForm,$params['filter']), 
                            array($column, $params['title'], $this->request[$column], $params['required'])
                        );
					}
				}
			}
		}
    }
    
    # model
    protected function doModel()
    {
        #echo "\r\n\r\n\r\n\r\n"."model========="."\r\n\r\n";
        if(is_array($this->model))
        {
            #print_r($this->model);
			foreach($this->model as $k => $v){
				if(!is_array($v)){
					$this->model[$k] = parent::compile($v);
				}
            }
            
            #print_r($this->model);
		}
    }

    # data
    protected function doQueryies()
    {
        #echo "\r\n\r\n\r\n\r\n"."queries========="."\r\n\r\n";

        # db
        $db = new DbMySqli();

        if(is_array($this->data))
        {
            #print_r($this->data);
            foreach($this->data as $k => $arg)
            {
                if(is_array($arg))
                {
                    if($arg['query_type'] == 'update'){
                        if(isset($arg['scheme']) && $arg['scheme'] !=''){
                            parent::compileUpdate($arg['scheme'],$arg['params'],$db);
                        }
                    }
                    
                    else if($arg['query_type'] == 'insert'){
                        if(isset($arg['scheme']) && $arg['scheme'] !=''){
                            parent::compileInsert($arg['scheme'],$arg['params'],$db);
                        }
                    }

                    else if($arg['query_type'] == 'post'){
                        if(isset($arg['scheme']) && $arg['scheme'] !=''){
                            $this->schemes[$k] = $arg['scheme'];
                            parent::compilePost($k ,$arg['scheme']);
                        }
                    }

                    else if($arg['query_type'] == 'delete'){
                        if(isset($arg['scheme']) && $arg['scheme'] !=''){
                            $this->schemes[$k] = $arg['scheme'];
                            parent::compileDelete($k ,$arg['scheme'],$db);
                        }else{
                            parent::compileDelete($k ,array(),$db);
                        }
                    }

                    else if($arg['query_type'] == 'single' || $arg['query_type'] == 'multiple')
                    {
                        #print_r($arg);
                        $query_string = parent::compileQuery($arg['query'],$arg['params']);
                        #echo $query_string."<----\r\n\r\n\r\n\r\n";
                    
                        $loop = array();
                        if($rlt = $db->query($query_string))
                        {
                            while($row = $rlt->fetch_assoc())
                            {
                                $loopModel = new UtilModel($row);
                                $this->row_data = $row;

                                # 스키마에 따른 데이터 변환
                                if(is_array($arg['scheme'])){
                                    foreach($arg['scheme'] as $fieldname => $fargv){
                                        foreach($fargv as $pk => $pv){
                                            if($arg['query_type'] != 'multiple'){
                                                $arg['scheme'][$fieldname][$pk] = $this->compile($pv);
                                            }
                                            if($pk =='value'){
                                                $datav = $this->compile($pv);
                                                $loopModel->{$fieldname} = $this->compile($datav);
                                            }
                                        }
                                        $this->row_data[$fieldname] = $loopModel->{$fieldname};                            
                                    }
                                }

                                $this->schemes[$k] = $arg['scheme'];

                                # then
                                if(isset($arg['then']) && is_array($arg['then']))
                                {
                                    # 2차 쿼리문 실행
                                    foreach($arg['then'] as $loop2_id => $loop2_params)
                                    {
                                        if($loop2_params['query_type'] == 'update'){
                                            if(isset($loop2_params['scheme']) && $loop2_params['scheme'] !=''){
                                                parent::compileUpdate($loop2_params['scheme'],$loop2_params['params'],$db);
                                            }
                                        } 
                                        
                                        else if($loop2_params['query_type'] =='insert'){
                                            if(isset($loop2_params['scheme']) && $loop2_params['scheme'] !=''){
                                                parent::compileInsert($loop2_params['scheme'],$loop2_params['params'],$db);
                                            }
                                        }

                                        else if($loop2_params['query_type'] =='delete'){
                                            if(isset($loop2_params['scheme']) && $loop2_params['scheme'] !=''){
                                                parent::compileDelete($loop2_params['scheme'],$loop2_params['params'],$db);
                                            }else{
                                                parent::compileDelete(array(),$loop2_params['params'],$db);
                                            }
                                        }

                                        else if($loop2_params['query_type'] == 'single' || $loop2_params['query_type'] == 'multiple')
                                        {
                                            $loop2 = array();
                                            $query_string2 = parent::compileQuery($loop2_params['query'],$loop2_params['params']);
                                            #echo $query_string2."\r\n\r\n\r\n\r\n";

                                            # 2차 배열
                                            $loopModel->{$loop2_id} = array();
                                            if($rlt2 = $db->query($query_string2))
                                            {
                                                while($row2 = $rlt2->fetch_assoc())
                                                {
                                                    $loop2Model = new UtilModel($row2);
                                                    foreach($row2 as $fieldname2 => $fieldval2){
                                                        if(isset($loop2_params['scheme'][$fieldname2]))
                                                        {
                                                            // print_r($params);
                                                            foreach($loop2_params['scheme'][$fieldname2] as $pk2 => $pv2){
                                                                $loop2Model->{$fieldname2} = $this->compile($pv2);
                                                            }
                                                        }else{
                                                            $loop2Model->{$fieldname2} = $this->compile($fieldval2);
                                                        }
                                                    }                                                        

                                                    # fetch
                                                    if($loop2_params['query_type'] =='single'){
                                                        $loop2 = $loop2Model->fetch();
                                                        break;
                                                    }else{
                                                        $loop2[] = $loop2Model->fetch();
                                                    }
                                                }
                                            }

                                            $loopModel->{$loop2_id} = $loop2;
                                        }
                                    }
                                }
                                
                                
                                # single type
                                #echo $arg['query_type']."\r\n";
                                if($arg['query_type'] =='single'){
                                    # fetch
                                    $loop = $loopModel->fetch();
                                    break;
                                }else{
                                    # fetch
                                    $loop[] = $loopModel->fetch();
                                }
                            }
                        }

                        # outmsg
                        if($arg['query_type'] =='single' && is_array($arg['outmsg']))
                        {
                            $msg_code = $arg['outmsg']['msg_code'];
                            $msg = $arg['outmsg']['msg'];

                            if($arg['outmsg']['mode'] =="true"){
                                if(count($loop)){
                                    throw new ErrorException(R::$sysmsg[$msg],0,0,$msg_code);
                                }
                            }else if($arg['outmsg']['mode'] =="false"){
                                if(count($loop)<1){
                                    throw new ErrorException(R::$sysmsg[$msg],0,0,$msg_code);
                                }
                            }
                        }
                        
                        $this->data[$k] = $loop;
                    }
				}
            }
		}
    }

    # alarm
    protected function doAlarm(){
        if(is_array($this->alarm))
        {
            if($this->alarm['state'])
            {
                $title = R::$strings['app_name'];
                $push_msg = $this->compile($this->alarm['msg']);
                $alarm_params = $this->compile($this->alarm['params']);

                # db
                $db = new DbMySqli();

                # 푸시 및 문자 전송 조건 체크문
                $_conditions = $this->alarm['condition'];

                # 조건 확인
                $_is_send = 'false';
                if(is_array($_conditions))
                {
                    foreach($_conditions as $condk => $condvargs)
                    {
                        if($this->request[$condk]){
                            $condi = trim($condvargs['condi']);
                            $condi_value = $condvargs['value'];
                            if($condi == '='){
                                if($this->request[$condk] == $condi_value){
                                    $_is_send = 'true';
                                }
                            }else if($condi == '<'){
                                if($this->request[$condk] < $condi_value){
                                    $_is_send = 'true';
                                }
                            }else if($condi == '<='){
                                if($this->request[$condk] < $condi_value || $this->request[$condk] == $condi_value){
                                    $_is_send = 'true';
                                }
                            }else if($condi == '>'){
                                if($this->request[$condk] > $condi_value){
                                    $_is_send = 'true';
                                }
                            }else if($condi == '>='){
                                if($this->request[$condk] > $condi_value || $this->request[$condk] == $condi_value){
                                    $_is_send = 'true';
                                }
                            }
                        }
                    }
                }

                if($_is_send=='true')
                {
                    $to = array();

                    # push 
                    $count = 0;
                    if(isset($this->alarm['send_option']) && $this->alarm['send_option'])
                    {
                        $qry = '';
                        switch($this->alarm['send_option']){
                            case 'all' :
                                $qry = sprintf("SELECT `userid` FROM `%s`", R::$tables['member']);
                            break;
                            case 'admin':
                                $qry = sprintf("SELECT `userid` FROM `%s` WHERE `level`>='100'", R::$tables['member']);
                            break;
                            case 'related':
                            break;
                        }

                        if($qry)
                        {
                            $rlt = $db->query($qry);
                            while($row = $rlt->fetch_row())
                            {
                                $to[] = $row[0];

                                # 알람메세지 저장
                                if(R::$tables['alarm'])
                                {
                                    #insert
                                    $db['userid']   = $row[0];
                                    $db['msg']      = $push_msg;
                                    $db['param']    = $alarm_params;
                                    $db['signdate'] = time();
                                    $db['isread']   = time();
                                    $db->insert(R::$tables['alarm']);
                                }
                            }
                        }

                        if(count($to))
                        {
                            # 푸시
                            if(isset($this->alarm['push']) && $this->alarm['push'])
                            {
                                if(_PUSH_PROJECTKEY_ && _PUSH_PROJECTKEY_ !=''){
                                    # 푸시전송
                                    $push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
                                    if(file_exists($push_sdk_root.'/config/config.push.php'))
                                    {
                                        include_once $push_sdk_root.'/config/config.push.php';
                                        include_once $push_sdk_root.'/classes/push/PushSend.class.php';

                                        try{
                                            # 보내기
                                            $pushSend = new PushSend(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
                                            $pushSend->push($to);
                                            $resp = $pushSend->send(array(
                                                'title' => $title,
                                                'msg'   => $push_msg
                                            ));
                                        
                                            #@ remaining_count : 잔여량
                                            #echo json_encode(array('result'=>'true', 'remaining_count'=>$resp['remaining_count'], 'msg'=>$resp['msg']));
                                        }catch(Exception $e){
                                            #echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
                                            // throw new ErrorException($e->getMessage(),__LINE__);
                                        }
                                    }
                                }
                            }

                            # 이메일
                            if(isset($this->alarm['mail']) && $this->alarm['mail']){

                            }

                            # 문자
                            if(isset($this->alarm['sms']) && $this->alarm['sms']){

                            }
                        }
                    }
                }
            }
        }
    }

    # output
    protected function doOutput()
    {
        // empty
        $this->row_data = array();

        #echo "\r\n\r\n\r\n\r\n"."output========="."\r\n\r\n";
        if(is_array($this->output))
        {
            #print_r($this->output);
			foreach($this->output as $k => $v){
				if(!is_array($v)){
					$this->output[$k] = parent::compile($v);
				}
            }
            
            #print_r($this->output);
		}
    }
}
?>