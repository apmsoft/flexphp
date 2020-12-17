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
$req->usePOST();

# Validation Check 예제
$form = new ReqForm();
$form->chkNull('usertoken', 'user token',$req->usertoken, true);
$form->chkNull('userid', 'id', $req->userid, true);
$form->chkNumber('evtid', '게임', $req->evtid, true);

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

# 이미 진행중인 게임인지 확인하기
$is_myevent = $db->get_record('id', R::$tables['myevents'], 
    sprintf("`evt_id`='%s' AND `muid`='%u' AND end_date is NULL", $req->evtid, $userinfo['id'])
);
if(!isset($is_myevent['id'])){
    out_json(array('result'=>'false','msg_code'=>'w_offgoing_game', 'msg'=>R::$sysmsg['w_offgoing_game']));
}

# update
$db['end_date'] = date('Y-m-d');
$db['end_time'] = date('H:i');
$db->update(R::$tables['myevents'], sprintf("`evt_id`='%u' AND `muid`='%u'", $req->evtid, $userinfo['id']));

# output
out_json( array(
    'result' => 'true',
    'msg'    => R::$sysmsg['v_end_game']
));
?>