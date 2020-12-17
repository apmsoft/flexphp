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
$form->chkEngNumUnderline('doc_id', 'DOC ID',$req->doc_id, true);

# Model
$model = new UtilModel();
$model->table    = '';
$filters_columns = array('id','signdate','up_date','recently_connect_date','logout_time','alarm_readdate','passwd','extract_id','authemailkey');

# resource
// R::init($req->lang);
R::parserResourceDefinedID('tables');
R::parserResourceDefinedID('manifest');

# columns :: list
$jc = array();
if(isset(R::$config['columns']['list'])){
	$jc =&R::$config['columns']['list'];
}

# manifest doc_id  체크
if(!isset(R::$manifest['feature']['document'][$req->doc_id])){
	out_json(array('result'=>'false','msg_code'=>'e_doc_id','msg'=>R::$sysmsg['e_doc_id']));
}

$manifest_doc_info = array();
$manifest_doc_info =& R::$manifest['feature']['document'][$req->doc_id];
$table_nickname = $manifest_doc_info['table'];
$model->table = R::$tables[$table_nickname];

# db 선언 및 접속
$db = new DbMySqli();

# columns
$columns = array();
$rlt = $db->query(sprintf("SELECT COLUMN_NAME,COLUMN_TYPE,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->table));
while($row = $rlt->fetch_assoc())
{
	$column_name = $row['COLUMN_NAME'];
	if(!in_array($column_name, $filters_columns)){
		$column_title= (isset($jc[$column_name])) ? $jc[$column_name]['title']: $row['COLUMN_COMMENT'];
		$columns[] = array(
			'name'        => $column_name,
			'column_type' => $row['COLUMN_TYPE'],
			'title'       => $column_title,
			'print'       => 'y'
		);
	}
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>$columns
));
?>
