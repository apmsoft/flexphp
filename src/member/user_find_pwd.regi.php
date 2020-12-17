<?php
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Db\DbMySqli;
use Fus3\String\StringRandom;
use Fus3\Mail\MailSendObject;
use Fus3\Util\UtilModel;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkName('name','이름', $req->name, true);
$form->chkEmail('userid','아이디(이메일)', $req->userid, true);

# resources
R::parserResourceDefinedID('tables');
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# model
$model = new UtilModel();
$model->authtime = time();

# passwd
$randomObj    = new StringRandom(R::$r->array['random_params']);
$random_pwd   = $randomObj->arrayRand(10);

# db
$db = new DbMySqli();

# query
$where = sprintf("`name`='%s' AND `userid`='%s'", $req->name, $req->userid);
$userinfo = $db->get_record('id', R::$tables['member'], $where);
if(!isset($userinfo['id']) || !$userinfo['id']){
	out_json(array('result'=>'false','msg_code'=>'e_usernotfound', 'msg'=>R::$sysmsg['e_usernotfound']));
}

# CipherEncrypt
$cipherEncrypt = new CipherEncrypt($userinfo['id'].'abcd****'.$model->authtime);
$cipher_authstr = $cipherEncrypt->_base64_urlencode();

# message
$message = str_replace(
	array('{rpwd}', '{url}'), 
	array($random_pwd, _SITE_HOST_.'/src/member/authpmailwd.php?'.$cipher_authstr),
	R::$sysmsg['i_findpwd_mailmsg']
);

# mail
$mailSendObj = new MailSendObject();
$mailSendObj->setTo($req->name, $req->userid);
$mailSendObj->setFrom(R::$strings['adm_email'], R::$strings['app_name']);
$mailSendObj->setTextPlain($message);
if(!$mailSendObj->send(R::$strings['app_name'].' 비밀번호 찾기')){
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
