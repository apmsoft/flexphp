<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\R\R;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Log\Log;

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
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkNumber('id', '회원번호',$req->id, true);

# resource
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();

# db
$db = new DbMySqli();

# 관리자 체크
$is_adm = $db->get_record('id,userid',R::$tables['admmem'],sprintf("`id`='%u'",$req->id));
if(!isset($is_adm['id'])){
	out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# delete
$db->delete(R::$tables['admmem'],sprintf("`id`='%u'",$req->id));

# 회원 체크
$is_user = $db->get_record('id',R::$tables['member'],sprintf("`userid`='%s'",$is_adm['userid']));
if(isset($is_user['id'])){
	$db['level'] = 1;
	$db->update(R::$tables['member'],sprintf("`id`='%u'",$is_user['id']));
}

# adm log
Log::init(R::$tables['adm_log']);
Log::d( sprintf("삭제 ADM 아이디(%s) ", $is_adm['userid']) );

# output
out_json( array(
    'result' => 'true',
    'msg'    => R::$sysmsg['v_delete']
));
?>
