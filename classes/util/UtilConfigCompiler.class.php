<?php
# ======================================================
# @Author   : 김종관 | 010-4023-7046
# @Email    : apmsoft@gmail.com
# @HomePage : https://www.fancyupsoft.com
# @Editor   : VSCode
# @version : 1.1
#----------------------------------------------------------
namespace Fus3\Util;

use Fus3\R\R;
use \ErrorException;
use Fus3\Db\DbHelperWhere;
use Fus3\Util\UtilMakeThumbnail;

class UtilConfigCompiler 
{
    public $request = array();

    # config
    protected $validation;
    public $authority;
    public $model;
    public $data;
    public $alarm;
    public $output;
    public $uploadable;
    public $schemes;

    # then parent data 임시
    protected $row_data;
    protected $parent_rows = array();

    /**
	* @var defines	: 사용자 선언 define 변수값
	* @var globals	: 전역변수에 선언된 값
	*/
	public $defines   = array();
    public $globals   = array('_SERVER','_COOKIE','_SESSION','_DEFINE');
    public $resources = array('__ARRAY__','__TABLES__','__SYSMSG__','__STRINGS__','__DOUBLUES__','__INTEGERS__',
    '__QUERIES__','__FLOATS__','__R__');

	/**
	 * @var funs : 사용할 수 있는 함수(인클루드 된 사용자 함수 포함) 목록
     * @var magicfuns : 예약 함수 
     * @var maincvars : 예약 변수 (특수목적)
	 */
	public $funs	= array();
    public $magicfuns = array('__REQ__','__FUNC__','__DATA__','__MODEL__','__UPLOADABLE__','__TEXT__','__SCHEME__');
    protected $magicvars = array('__FIELD__','__Q__');

    public function __construct()
    {
        // 사용자 함수
        if(function_exists('get_defined_functions')){
            $splfunargs = array();
            $splfunargs=get_defined_functions();
            $this->funs=array_merge($splfunargs['user'],$this->funs);
            $this->funs=array_unique($this->funs);
            #print_r($this->funs);
        }

        // 사용자 상수
        if(function_exists('get_defined_constants')){
            if(count($this->defines)<1){
                $const = get_defined_constants(true);
                if(is_array($const['user'])){
                    $this->defines = $const['user'];
                }
            }
        }
    }

    public function compile($var)
    {
        if(is_array($var)){
            return $var;
            #echo print_r($var)." \r\n";
        }

        preg_match_all("|{[^}](.*)[^}]+}|U",$var,$matches,PREG_PATTERN_ORDER);
        if(is_array($matches) && isset($matches[0]))
		{
            $binding_params = $matches[0];
            #echo $var."\r\n";
            if(count($binding_params))
            {
                // print_r($binding_params);
                $val = strtr($binding_params[0], array('{'=>'','}'=>''));
                #echo $val."\r\n";
                if(strpos($val,'.') !==false)
                {
                    $val_argv = explode('.', $val);
                    $bind_name = $val_argv[0];
                    $bind_val1  = $val_argv[1];
                    #print_r($val_argv);

                    # 함수인지 먼저 체크
                    if(array_search($bind_name,$this->funs) !== false)
                    {
                        #echo "is function"."\r\n";
                        $var = call_user_func_array(
                            array($bind_name), 
                            array($val)
                        );
                    
                    # 글로벌 변수 인지 체크
                    } else if(array_search($bind_name,$this->globals) !== false){
                        #echo "is global : ". $bind_name. ' : '. $bind_val1 ."\r\n";

                        if( $bind_name == '_SESSION'){
                            $var = (isset($_SESSION[$bind_val1])) ? $_SESSION[$bind_val1]: '';
                        }else if( $bind_name == '_COOKIE'){
                            $var = (isset($_COOKIE[$bind_val1])) ? $_COOKIE[$bind_val1]: '';
                        }else if( $bind_name == '_SERVER'){
                            $var = (isset($_SERVER[$bind_val1])) ? $_SERVER[$bind_val1]: '';
                        }else if( $bind_name == '_DEFINE'){
                            $var = (isset($this->defines[$bind_val1])) ? $this->defines[$bind_val1]: '';
                        }
                    
                    # 리소스 변수 인지 체크
                    } else if(array_search($bind_name,$this->resources) !== false){
                        #echo "is resources". $bind_name.' / '.$bind_val1."\r\n";
                        $res_name = strtolower(strtr($bind_name,array('_'=>'')));
                        #echo $res_name."\r\n";
                        if($res_name =='r'){
                            #echo $bind_val1."\r\n";
                            if($bind_val1 == 'array'){
                                if(!count(R::$r->array)){
                                    R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');
                                }
                                $var = R::$r->array;
                            }else if($bind_val1 == 'manifest_adm'){
                                R::parserResource(_ROOT_PATH_.'/'._RES_.'/manifest_adm.json', 'madm');
                                $var = R::$r->madm;
                            }else {
                                if(!count(R::${$bind_val1})){
                                    R::parserResourceDefinedID($bind_val1);
                                }
                                $var = R::${$bind_val1};
                            }
                            #print_r($var);
                        }else{
                            if($res_name == 'array'){
                                if(!count(R::$r->array)){
                                    R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');
                                }
                                $var = R::$r->array[$bind_val1];
                            }else{
                                if(!count(R::${$res_name})){
                                    R::parserResourceDefinedID($res_name);
                                }
                                $var = R::${$res_name}[$bind_val1];
                            }
                        }
                    } else if(array_search($bind_name,$this->magicfuns) !== false){
                        #echo "is magicfuns ".$val."\r\n";
                        if($bind_name == '__REQ__')
                        {
                            $var  = (isset($this->request[$bind_val1])) ? $this->request[$bind_val1] : '';
                        }
                        else if($bind_name == '__FUNC__')
                        {
                            #print_r($binding_params);
                            $var = $this->compileFunc($bind_val1 ,array_slice($binding_params, 1));
                        }
                        else if($bind_name == '__TEXT__')
                        {
                            // print_r($val_argv);
                            $_count_val = count($val_argv);
                            $var = ($_count_val <3) ? $bind_val1 : implode('.',array_slice($val_argv, 1, $_count_val));
                        }
                        else if($bind_name == '__SCHEME__')
                        {
                            // echo $bind_val1. "\r\n";
                            // print_r($this->schemes[$bind_val1]);
                            $_schemes = array();
                            if(isset($this->schemes[$bind_val1])) {
                                if(is_array($this->schemes[$bind_val1])){
                                    // foreach($this->schemes[$bind_val1] as $sid => $sarg){
                                    //     $_schemes[$sid] = array();
                                    //     #$_schemes[$sid] = array_diff_key($sarg,array("value"=>''));
                                    //     $_schemes[$sid] = array_diff_key($sarg,array("value"=>''));
                                    // }
                                    $_schemes = $this->schemes[$bind_val1];
                                }
                            }
                            $var = $_schemes;
                        }
                        else if($bind_name == '__DATA__')
                        {
                            // echo "is __DATA__". $bind_val1. "\r\n";
                            // echo "3 2\r\n";
                            // print_r($val_argv);
                            // print_r($this->data);
                            if(isset($val_argv[2]))
                            {
                                $bind_val2 = $val_argv[2];

                                if($bind_val2 =='fetch()'){
                                    $var = $this->data[$bind_val1];
                                }else{
                                    if(isset($this->data[$bind_val1])){
                                        $var = $this->data[$bind_val1][$bind_val2];
                                    }else if(count($this->row_data)){
                                        $var =$this->row_data[$bind_val2];
                                    }
                                }
                            }else{
                                # 현재 속해 있는 데이터 값 __DATA__.id
                                if(count($this->row_data)){
                                    #echo $this->row_data[$bind_val1]." :: then :: \r\n";
                                    $var =$this->row_data[$bind_val1];
                                }
                            }
                        }
                        else if($bind_name == '__MODEL__'){
                            if(isset($this->model[$bind_val1])){
                                $var = $this->model[$bind_val1];
                            }
                        }
                        else if($bind_name == '__UPLOADABLE__'){
                            // print_r($val_argv);
                            // echo $bind_name. ' '.$bind_val1.PHP_EOL;
                            // echo $this->uploadable[$bind_val1].PHP_EOL;
                            if(isset($this->uploadable[$bind_val1])){
                                $var = $this->uploadable[$bind_val1];
                            }
                        }
                    }
                }
            }
        }
    return $var;
    }

    public function compileInsert(array $scheme, array $qry_params, &$db)
    {
        if(is_array($scheme))
        {
            $files_columns = array();
            
            foreach($scheme as $fieldname => $params)
            {
                foreach($params as $pk => $pv)
                {
                    $val = $this->compile($pv);
                    if($pk == 'value')
                    {
                        $params[$pk] = $this->compile($pv);

                        # 첨부파일인지 체크
                        #print_r($params);
                        if(isset($params['type']) && $params['type']=='file')
                        {
                            if(!isset($params['table']) || $params['table']==''){
                                throw new ErrorException($fieldname.' :table name :'. R::$sysmsg['e_null'],0,0,'e_null',__LINE__);
                            }

                            if(!isset($params['image_size']) || $params['image_size']==''){
                                throw new ErrorException($fieldname.' :image_size :'. R::$sysmsg['e_null'],0,0,'e_null',__LINE__);
                            }

                            $files_columns[$fieldname] = array(
                                'table' => $this->compile( $params['table'] ),
                                'image_size' => $this->compile( $params['image_size'] ),
                                'value' => $val
                            );
                        }

                        # 설정값 안이 데이터인지 체크
                        if(isset($params['default']) && is_array($params['default'])){
                            if($val !=''){
                                if(!isset($params['default'][$val])){
                                    throw new ErrorException($fieldname.' '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
                                }
                            }
                        }

                        $db[$fieldname] = $val;
                    }
                }
            }

            # table
            #print_r($qry_params);
            $_table = $this->compile($qry_params['table']);
            #echo $_table."\r\n";
            
            # insert
            $db->insert($_table);

            # file
            if(count($this->uploadable)){
                if(is_array($files_columns)){
                    foreach($files_columns as $upk => $upargv){
                        $this->doEditUploadable ($upargv['table'], $upargv['value'], $upargv['image_size'], $db);
                    }
                }
            }
        }
    }

    public function doEditUploadable ($table, $extract_id, $image_size, &$db){
        # query
        $is_upfiles_qry = sprintf(
            "SELECT id,file_type,directory,sfilename FROM `%s` WHERE extract_id='%s' AND `is_regi`<'1'",
            $table, $extract_id
        );

        # 썸네일 만들기
        if($file_rlt = $db->query($is_upfiles_qry)){
            $utilMakeThumbnail = new UtilMakeThumbnail;
            while($file_row = $file_rlt->fetch_assoc()){
                $utilMakeThumbnail->makeThumbnail($file_row, $image_size['thumbnail'],$image_size['middle']);
            }
        }

        #files update
        $regi_upfiles_qry = sprintf(
            "UPDATE `%s` SET `is_regi`='1' WHERE `extract_id`='%s' AND `is_regi`<'1'",
            $table, $extract_id
        );
        $db->query($regi_upfiles_qry);
    }

    public function compileUpdate(array $scheme, array $qry_params, &$db)
    {
        if(is_array($scheme))
        {
            $files_columns = array();
            
            foreach($scheme as $fieldname => $params)
            {
                foreach($params as $pk => $pv)
                {
                    $val = $this->compile($pv);
                    if($pk == 'value')
                    {
                        $params[$pk] = $this->compile($pv);

                        # 첨부파일인지 체크
                        #print_r($params);
                        if(isset($params['type']) && $params['type']=='file')
                        {
                            if(!isset($params['table']) || $params['table']==''){
                                throw new ErrorException($fieldname.' :table name :'. R::$sysmsg['e_null'],0,0,'e_null',__LINE__);
                            }

                            if(!isset($params['image_size']) || $params['image_size']==''){
                                throw new ErrorException($fieldname.' :image_size :'. R::$sysmsg['e_null'],0,0,'e_null',__LINE__);
                            }

                            $files_columns[$fieldname] = array(
                                'table' => $this->compile( $params['table'] ),
                                'image_size' => $this->compile( $params['image_size'] ),
                                'value' => $val
                            );
                        }

                        # 설정값 안이 데이터인지 체크
                        if(isset($params['default']) && is_array($params['default'])){
                            if($val !=''){
                                if(!isset($params['default'][$val])){
                                    throw new ErrorException($fieldname.' '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
                                }
                            }
                        }

                        $db[$fieldname] = $val;
                    }
                }
            }

            # table
            #print_r($qry_params);
            $_table = $this->compile($qry_params['table']);
            #echo $_table."\r\n";

            # where
            $_where = $this->compileDbWhere($qry_params['where']);
            #echo $_where."\r\n";
            #print_r($db->params);
            #exit;
            
            # update
            $db->update($_table, $_where);

            # file
            if(count($this->uploadable)){
                if(is_array($files_columns)){
                    foreach($files_columns as $upk => $upargv){
                        $this->doEditUploadable ($upargv['table'], $upargv['value'], $upargv['image_size'], $db);
                    }
                }
            }
        }
    }

    public function compileDelete(array $scheme, array $qry_params, &$db)
    {
        if(is_array($scheme))
        {
            $files_columns = array();
            
            foreach($scheme as $fieldname => $params)
            {
                foreach($params as $pk => $pv)
                {
                    $val = $this->compile($pv);
                    if($pk == 'value')
                    {
                        $params[$pk] = $this->compile($pv);

                        # 설정값 안이 데이터인지 체크
                        if(isset($params['default']) && is_array($params['default'])){
                            if($val !=''){
                                if(!isset($params['default'][$val])){
                                    throw new ErrorException($fieldname.' '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
                                }
                            }
                        }
                    }
                }
            }

            # table
            #print_r($qry_params);
            $_table = $this->compile($qry_params['table']);
            #echo $_table."\r\n";

            # where
            $_where = $this->compileDbWhere($qry_params['where']);
            #echo $_where."\r\n";
            #print_r($db->params);
            #exit;
            
            # update
            $db->delete($_table, $_where);
        }
    }

    public function compilePost($data_id, array $scheme){
        if(is_array($scheme))
        {
            foreach($scheme as $fieldname => $params)
            {
                // print_r($params);
                foreach($params as $pk => $pv){
                    // $data_format =&$pv;
                    #echo 'compilePost '.$data_format." \r\n";
                    $this->schemes[$data_id][$fieldname][$pk] = $this->compile($pv);
                }
            }
        }
    }

    public function compileQuery($tpl_query, $tpl_params)
    {
        $binding_query = '';

        # query template 확인
        if(!$tpl_query){
            throw new ErrorException('QUERY '. R::$sysmsg['e_null'],0,0,'query',__LINE__);
        }

        # query 가져오기
        $tpl_query = $this->compile($tpl_query);
        if(!$tpl_query){
            throw new ErrorException('QUERY '. R::$sysmsg['e_db_unenabled'],0,0,'e_db_unenabled',__LINE__);
        }
        #echo $tpl_query."\r\n";
        $tpl_query_params_count = 0;
        $tpl_params_count = count($tpl_params);
        #echo $tpl_params_count."\r\n";

        # 쿼리문안에 있는 파라메터 갯수 체크
        preg_match_all("|{[^}](.*)[^}]+}|U",$tpl_query,$matches,PREG_PATTERN_ORDER);        
        if(is_array($matches) && isset($matches[0]))
		{
            $tpl_query_params_count = count($matches[0]);
            $pattern_params = array();

            # params와 query params 갯수가 일치하는지 체크
            if($tpl_params_count != $tpl_query_params_count){
                throw new ErrorException('QUERY pramater count '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
            }

            # params 테이블정보 가져오기
            foreach($matches[0] as $pk => $pv){
                #echo $pv."\r\n";
                $tpl_params_key = strtr($pv, array('{'=>'','}'=>''));
                $tpl_params_val = $tpl_params[$tpl_params_key];
                if(is_array($tpl_params_val)){
                    $val = $this->compileDbWhere($tpl_params_val);
                    if($val){
                        $val = 'WHERE '.$val;
                    }
                }else{
                    $val = $this->compile($tpl_params_val);
                }
                $pattern_params[$pv] = $val;
            }
            #print_r($pattern_params);

            # binding
            $binding_query = strtr($tpl_query, $pattern_params);
        }
    return $binding_query;
    }

    public function compileDbWhere(array $arg)
    {
        $binding_where = '';

        if(!is_array($arg)){
            throw new ErrorException('is not array WHERE prameter '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
        }

        # 매직변수
        $fields = '';        
        if(isset($arg['__FIELD__'])){
            $fields = $this->compile($arg['__FIELD__']);
        }

        $_q = '';
        if(isset($arg['__Q__'])){
            $_q = $this->compile($arg['__Q__']);
        }
        
        unset($arg['__FIELD__'], $arg['__Q__']);
        $dbHelperWhere = new DbHelperWhere($fields, $_q);
        foreach($arg as $column_id => $wh_arg)
        {
            $current_value = $this->compile($wh_arg['value']);
            #echo $current_value." << === where : \r\n";
            $dbHelperWhere->setBuildWhere($column_id,$wh_arg['condition'], $current_value, $wh_arg['coord']);
        }
        // print_r($dbHelperWhere->where);
        if($dbHelperWhere->where){
            $binding_where = $dbHelperWhere->where;
        }
    return $binding_where;
    }

    public function compileFunc($func, array $arg)
    {
        $result = '';

        if(!is_array($arg)){
            throw new ErrorException('is not array FUNC prameter '. R::$sysmsg['e_isnot_match'],0,0,'e_isnot_match',__LINE__);
        }

        $func_params = array();
        foreach($arg as $fk => $fv){
            $func_params[] = $this->compile($fv);
        }
        #print_r($func_params);

        #echo $func."\r\n";
        $func_run_result = call_user_func_array(
            $func, 
            $func_params
        );

        switch($func){
            case 'limit' :
                $this->model = $this->model + $func_run_result;
                $result = $this->model['limitStart'].','.$this->model['limitEnd'];
            break;
            default :
                $result = $func_run_result;
        }

    return $result;
    }
}
?>