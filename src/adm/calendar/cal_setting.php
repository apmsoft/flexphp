<?php
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
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkAlphabet('lang', '언어',$req->lang, false);
$form->chkNull('doc_id', 'doc_id', $req->doc_id, true);

# Model
// $model = new UtilModel();

# db 선언 및 접속
// $db = new DbMySqli();

# resource
R::init($req->lang);
R::parserResourceDefinedID('manifest');

# content
$manifest =&R::$manifest['feature']['document'];
if(!isset($manifest[$req->doc_id])){
	out_json(array('result'=>'false', 'msg_code'=>'e_doc_id','msg'=>'[calendar] '.R::$sysmsg['e_doc_id']));
}

# output
out_json(array(
	'result' =>'true',
	'title'  =>$manifest[$req->doc_id]['title'],
	'msg'    =>$manifest[$req->doc_id]['config']
));
?>
