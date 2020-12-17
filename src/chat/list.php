<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
// use \ErrorException;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# Validation Check 예제
$form = new ReqForm();
$form->chkNull('usertoken', 'user token',$req->usertoken, true);
$form->chkNull('userid', 'id', $req->userid, true);
$form->chkNumber('evtid', '게임', $req->evtid, true);
$form->chkNumber('min_id', 'min id', $req->min_id, true);
$form->chkNumber('max_id', 'max id', $req->max_id, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->min_id = $req->min_id;
$model->max_id = $req->max_id;
$model->where = sprintf("`userid`='%s'",$req->userid);

# db
$db = new DbMySqli();

# 회원가입자 인지 체크
$userinfo = $db->get_record('id,authtoken', R::$tables['member'], $model->where);
if(!isset($userinfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
}

# 토큰비교
if($userinfo['authtoken'] != $req->usertoken){
    out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match', 'msg'=>R::$sysmsg['w_token_isnot_match']));
}

# 이미 진행중인 게임인지 확인하기
// $is_myevent = $db->get_record('id', R::$tables['myevents'], 
//     sprintf("`evt_id`='%s' AND `muid`='%u' AND end_date is NULL", $req->evtid, $userinfo['id'])
// );
// if(!isset($is_myevent['id'])){
//     out_json(array('result'=>'false','msg_code'=>'w_offgoing_game', 'msg'=>R::$sysmsg['w_offgoing_game']));
// }

# 챗 목록
$loop = array();
$qry = sprintf(
    "SELECT id,evtid,missonid,mymisson_token,signdate FROM `%s` WHERE `evtid`='%s' AND `muid`='%u' AND `id` > '%u' ORDER BY `id` ASC", 
    R::$tables['misson_chat'], $req->evtid, $userinfo['id'], $req->max_id
);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc())
{
    $loopModel = new UtilModel($row);
    $loopModel->msg         = '';
    $loopModel->description = '';
    $loopModel->linkurl     = '';
    $loopModel->files       = array();
    // $loopModel->images      = array();
    $loopModel->name        = ''; 
    $loopModel->misson_way  = '';
    $loopModel->signdate    = date('Y.m.d H:i', $loopModel->signdate);

    # 미션 답변
    if($loopModel->mymisson_token != 'n'){
        $mymisson_info = $db->get_record('id,missonid,answer', R::$tables['mymisson'], sprintf("`misson_token`='%s'", $loopModel->mymisson_token));
        if(isset($mymisson_info['id'])){
            $loopModel->msg = $mymisson_info['answer'];

            # 카메라미션
            $misson_info = $db->get_record('id,misson_way', R::$tables['misson'], sprintf("`id`='%u'", $mymisson_info['missonid']));
            $loopModel->misson_way  = $misson_info['misson_way'];
            if($misson_info['misson_way'] == 'camera')
            {
                $loopModel->msg = _SITE_HOST_.$mymisson_info['answer'];
            }
        }
    }else{
        # 미션 정보
        $misson_info = $db->get_record('id,msg,misson_way,linkurl,description,misson_token,extract_id,dvmac', R::$tables['misson'], sprintf("`id`='%u'", $loopModel->missonid));
        if(isset($misson_info['id']))
        {
            $loopModel->msg         = $misson_info['msg'];
            $loopModel->description = contextType($misson_info['description'], 'XSS');
            // $loopModel->linkurl     = $misson_info['linkurl'];
            $loopModel->name        = R::$strings['app_name'];
            $loopModel->misson_way  = $misson_info['misson_way'];

            # 첨부파일
            $files = array();
            $images = array();
            $upfiles_qry =sprintf("SELECT id,file_type,directory,sfilename,ofilename,extract_id FROM `%s` WHERE %s", R::$tables['misson_upfiles'], sprintf("`extract_id`='%s' AND `is_regi`='1'", $misson_info['extract_id']));
            $upfiles_rlt = $db->query($upfiles_qry);
            while($upfiles_row = $upfiles_rlt->fetch_assoc()){
                $file_types = explode('/',$upfiles_row['file_type']);
                if($file_types[0] != 'image'){
                    $files[] = array(
                        'ofilename' => $upfiles_row['ofilename'],
                        'id' => $upfiles_row['id'],
                        'extract_id' => $upfiles_row['extract_id']
                    );
                    
                }
            }

            $loopModel->files = $files;
        }
    }

    # min
    if($loopModel->id < $model->min_id){
        $model->min_id = $loopModel->id;
    }

    # max
    if($loopModel->id > $model->max_id){
        $model->max_id = $loopModel->id;
    }

    # loop에 배열값 담기
    $loop[] = $loopModel->fetch();    
}

# output
out_json( array(
    'result' => 'true',
    'max_id' => $model->max_id,
    'min_id' => $model->min_id,
    'msg'    => $loop
));
?>