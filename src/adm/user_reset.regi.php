<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\R\R;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://fancy-up.tistory.com
| @Editor	: Sublime Text 3 (기본설정)
| @UPDATE	: 0.5
| @TITLE 	: php 개발 가이드 (종합)
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERADMIN_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();

# Model
// $model = new UtilModel();

# db 선언 및 접속
$db = new DbMySqli();

# resource
R::parserResourceDefinedID('tables');

# alarm_read_date
$row = $db->get_record('id', R::$tables['member'],sprintf("`id`='%s'", $req->id));
if(!isset($row['id'])){
	out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# query sample
$db['level'] = '1';
$db->update(R::$tables['member'],sprintf("`id`='%s'", $req->id));

# output
out_json(array(
	'result' =>'true',
	'msg'    =>R::$sysmsg['v_delete']
));
?>
