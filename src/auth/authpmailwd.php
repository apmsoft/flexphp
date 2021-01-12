<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Cipher\CipherDecrypt;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
use Fus3\Date\DateTimes;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# model
$model = new UtilModel();
$model->decrypt_str = '';
$model->auth_time   = 0;
$model->auth_str    = '';
$model->auth_id     = 0;

# find key
$req_params = $req->fetch();

if(is_array($req_params)){
	$x=0;
	foreach($req_params as $k=>$v){
		if($x>0){
			break;
		}

		# CipherDecrypt
		$cipherDecrypt = new CipherDecrypt($k);
		$model->decrypt_str= $cipherDecrypt->_base64_urldecode();
		$x++;
	}
}

# 폼및 request값 체크
$form = new ReqForm();
$form->chkNull('authemailkey','인증문자', $model->decrypt_str, true);
// echo $model->decrypt_str;

# decode
preg_match("/([0-9]+)$/", $model->decrypt_str, $out);
// out_r($out);
$model->auth_time = $out[0];
$model->auth_str = preg_replace("/{$out[0]}$/", "", $model->decrypt_str);
$model->auth_id  = preg_replace("/[^0-9]*/s", "", $model->auth_str);

# 2차 check validation 
$form->chkNumber('auth_id','인증번호', $model->auth_id, true);
$form->chkNull('auth_str','인증문자', $model->auth_str, true);
$form->chkNumber('auth_time','인증날짜', $model->auth_time, true);

# resources
R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# query
$where = sprintf("`id`='%s'", $model->auth_id);
$userinfo = $db->get_record('id,recently_connect_date,authemailkey', R::$tables['member'], $where);
if(!isset($userinfo['id']) || !$userinfo['id']){
	history_go(R::$sysmsg['e_usernotfound']);
}

# 날짜 비교
if($userinfo['recently_connect_date'] != $model->auth_time){
	history_go(R::$sysmsg['e_db_unenabled']);
}

# 당일인지 체크
$findedtime = date('Ymd',$model->auth_time);
$todate = date('Ymd');
if($findedtime != $todate){
	history_go('유효기간이 지났습니다');
}

# 문자가 일치하는지 체크
$strcmpstr = $userinfo['id'].'abcd****'.$userinfo['recently_connect_date'];
if($strcmpstr != $model->decrypt_str){
	history_go('인증문자가 일치하지 않습니다.');
}

# CipherEncrypt
$cipherEncrypt = new CipherEncrypt($userinfo['authemailkey']);
$encrypt_passwd = $cipherEncrypt->_md5_base64();

# 정보 업데이트
$db['passwd']                = $encrypt_passwd;
$db['recently_connect_date'] = time();
$db['up_date']               = time();
$db['authemailkey']          = '';
$db->update(R::$tables['member'], $where);

# output msg
window_location(_SITE_HOST_, '비밀번호를 '.R::$sysmsg['v_update']);
?>
