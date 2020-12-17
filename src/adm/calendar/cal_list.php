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
if($auth->level <_AUTH_SUPERADMIN_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();

# Model
$model = new UtilModel();

# resource
R::parserResourceDefinedID('manifest');
R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# manifest document
$loop = array();
$document_arg = &R::$manifest['feature']['document'];
if(is_array($document_arg)){
	foreach($document_arg as $doc_id => $doc_attr){
		if(is_array($doc_attr) && isset($doc_attr['category']) && $doc_attr['category']=='calendar')
		{
			# 게시판 총 갯수
			$this_table_id = $doc_attr['table'];
			$total_record  = $db->get_total_record(R::$tables[$this_table_id], '');
			
			# set item array
			$doc_attr['doc_id']       = $doc_id;
			$doc_attr['total_record'] = number_format($total_record);
			$loop[] = $doc_attr;
		}
	}
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>$loop
));
?>
