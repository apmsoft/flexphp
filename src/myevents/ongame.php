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

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
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

# 이미 진행중인 게임 목록
$loop = array();
$qry = sprintf("SELECT id,evt_id FROM `%s` WHERE `muid`='%u' AND end_date is NULL", R::$tables['myevents'], $userinfo['id']);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc()){
    $loopModel = new UtilModel($row);
    $loopModel->title       = '';
    $loopModel->start_date  = '';
    $loopModel->end_date    = '';
    $loopModel->description = '';
    // $loopModel->upfiles     = array();

    # 이벤트 정보
    $event_info = $db->get_record('id,title,start_date,end_date,description', R::$tables['events'], sprintf("`id`='%u'", $loopModel->evt_id));
    if(isset($event_info['id'])){
        $loopModel->title = $event_info['title'];
        $loopModel->start_date = $event_info['start_date'];
        $loopModel->end_date = $event_info['end_date'];
        $loopModel->description = str_cut($event_info['description'], 40);

        # 첨부파일 사진
        // $upfiles_info = $db->get_record('id,file_type,directory,sfilename', R::$tables['events_upfiles'], sprintf("`extract_id`='%s' AND `is_regi`='1'", $event_info['extract_id']));
        // if(isset($upfiles_info['id'])){
        //     $loopModel->upfiles = $upfiles_info;
        // }
    }

    # loop에 배열값 담기
    $loop[] = $loopModel->fetch();    
}

# output
out_json( array(
    'result' => 'true',
    'msg'    => $loop
));
?>