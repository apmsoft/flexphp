<?php 
use Fus3\R\R;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new Fus3\Auth\AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if($auth->id){
    out_json(array('result'=>'false', 'msg_code'=>'w_stay_logged_in','msg'=>R::$sysmsg['w_stay_logged_in']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Fus3\Req\Req;
$req->usePOST();

# Validation Check 예제
$form = new Fus3\Req\ReqForm();
$form->chkEmail('userid', '아이디', $req->userid, true);
$form->chkName('name', '이름', $req->name, true);
$form->chkPhone('cellphone', '휴대폰번호', $req->cellphone, true);

# resources
R::parserResourceDefinedID('tables');
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# model
$model = new Fus3\Util\UtilModel();
$model->authtime = time();

# db
$db = new Fus3\Db\DbMySqli();

# 이미 등록된 아이디 체크
$userinfo = $db->get_record('id,userid,name,cellphone,level', R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
if(!isset($userinfo['id'])){
    out_json( array('result'=>'false', 'msg_code'=>'e_usernotfound', 'msg'=>R::$sysmsg['e_usernotfound']) );
}

# 등급체크
if($userinfo['level'] < 1){
	out_json( array('result'=>'false', 'msg_code'=>'w_not_have_permission', 'msg'=>R::$sysmsg['w_not_have_permission']) );
}

# 이름 비교
$form->chkEquals('name', '이름',array($userinfo['name'], $req->name), true);

# 휴대폰번호 비교
$form->chkEquals('cellphone', '휴대폰번호',array(strtr($userinfo['cellphone'],array('-'=>'')), strtr($req->cellphone,array('-'=>''))), true);

# 임시 비밀번호 생성
$randomObj    = new Fus3\String\StringRandom(R::$r->array['random_params']);
$random_pwd   = $randomObj->arrayRand(10);

# CipherEncrypt
$cipherEncrypt = new Fus3\Cipher\CipherEncrypt($userinfo['id'].'abcd****'.$model->authtime);
$cipher_authstr = $cipherEncrypt->_base64_urlencode();

# message
$message = str_replace(
	array('{rpwd}', '{url}'), 
	array($random_pwd, _SITE_HOST_.'/src/auth/authpmailwd.php?'.$cipher_authstr),
	R::$sysmsg['i_findpwd_mailmsg']
);

# mail
$mailSendObj = new Fus3\Mail\MailSendObject();
$mailSendObj->setTo($req->name, $req->userid);
$mailSendObj->setFrom(R::$strings['adm_email'], R::$strings['app_name']);
$mailSendObj->setTextPlain($message);
if(!$mailSendObj->send(R::$strings['app_name'].' '.R::$strings['findpwd'])){
	out_json(array('result'=>'false','msg'=>R::$sysmsg['w_donot_sendmail']));
}else{
	$db['recently_connect_date'] = $model->authtime;
	$db['authemailkey']          = $random_pwd;
	$db->update(R::$tables['member'], sprintf("`id`='%s'", $userinfo['id']));

	# output
	out_json(array(
		'result' =>'true',
		'msg'    =>R::$sysmsg['v_findpwd_sendmail']
	));
}
?>