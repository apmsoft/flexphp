<?php
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
// use \ErrorException;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

# 세션시작
$auth = new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인상태 체크
if(!$auth->id){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission', 'msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# Validation Check 예제
$form = new ReqForm();
// $form->chkNull('usertoken', 'user token',$req->usertoken, true);
// $form->chkNull('userid', 'id', $req->userid, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->where = sprintf("`id`='%s'",$auth->id);
$model->columns = 'id,is_push'; //'id,is_push,is_marketing,authtoken';

# db
$db = new DbMySqli();

# 회원가입자 인지 체크
$userinfo = $db->get_record($model->columns, R::$tables['member'], $model->where);
if(!isset($userinfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_usernotfound', 'msg'=>R::$sysmsg['e_usernotfound']));
}

# 토큰비교
// if($userinfo['authtoken'] != $req->usertoken){
//     out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match', 'msg'=>R::$sysmsg['w_token_isnot_match']));
// }

$userinfo['app_version_str'] = R::$strings['app_version_str'];

# output
out_json( array(
    'result' => 'true',
    'msg'    => $userinfo
));
?>