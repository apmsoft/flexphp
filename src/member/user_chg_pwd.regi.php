<?php
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인상태 체크
if(!$auth->id){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission', 'msg'=>R::$sysmsg['w_not_have_permission']));
}

# 변수
$req = new Req;
$req->usePOST();

# Validation Check 예제
$form = new ReqForm();
// $form->chkNull('usertoken', 'user token',$req->usertoken, true);
// $form->chkEmail('userid', 'id', $req->userid, true);
$form->chkPasswd('passwd', '현재 비밀번호',$req->passwd, true);

$form->chkPasswd('new_passwd', '새 비밀번호', $req->new_passwd, true);
$form->chkEquals('re_new_passwd', '새 비밀번호 확인',array($req->new_passwd, $req->re_new_passwd), true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->authtime = time();
$model->where = sprintf("`id`='%s'",$auth->id);
// $model->where = sprintf("`userid`='%s'",$req->userid);

# db
$db = new DbMySqli();

# 회원가입자 인지 체크
$userinfo = $db->get_record('id,passwd', R::$tables['member'], $model->where);
if(!isset($userinfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
}
// $userinfo = $db->get_record('id,authtoken', R::$tables['member'], $model->where);
// if(!isset($userinfo['id'])){
//     out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
// }

# 토큰비교
// if($userinfo['authtoken'] != $req->usertoken){
//     out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match', 'msg'=>R::$sysmsg['w_token_isnot_match']));
// }

# 체크 현재 비밀번호 비교
if($userinfo['passwd'] != password($req->passwd)){
	out_json(array('result'=>'false','msg_code'=>'w_password_not_match', 'msg'=>R::$sysmsg['w_password_not_match']));
}

# 비밀번호 변경
$db['passwd'] = password($req->new_passwd);
$db['recently_connect_date'] = $model->authtime;
$db->update(R::$tables['member'], $model->where);

# output
out_json(array(
	'result' => 'true',
	'mag' => R::$sysmsg['v_chg_passwd']
));
?>
