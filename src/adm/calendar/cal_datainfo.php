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
$form->chkNull('doc_id', 'doc_id', $req->doc_id, true);

# Model
$model = new UtilModel();
$model->cal_info = array();
$model->table = '';
$max_view_data = array();
$max_cmt_data = array();

# resource
R::parserResourceDefinedID('manifest');
R::parserResourceDefinedID('tables');

# manifest document
$document_arg = &R::$manifest['feature']['document'];
if(is_array($document_arg)){
	foreach($document_arg as $doc_id => $doc_attr){
		if(is_array($doc_attr) && isset($doc_attr['category']) && $doc_attr['category']=='calendar'){
			if( $doc_id == $req->doc_id){
				$doc_attr['doc_id'] = $doc_id;
				$model->cal_info = $doc_attr;
			}
		}
	}
}

# check config
if(!isset($model->cal_info['config']) || !$model->cal_info['config']){
	out_json(array('result'=>'false','msg'=>'It is not config filename'));
}

# db
$db = new DbMySqli();

# config json
// R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$model->cal_info['config'].'.json', 'config');

# 최고 조회수 많은 글
$model->table = $model->cal_info['table'];
$max_view_data = $db->get_record('id,signdate,title,view_count,start_date,end_date', R::$tables[$model->table], "`view_count`>0 ORDER BY view_count DESC LIMIT 0,1");
if(isset($max_view_data['id'])){
	$max_view_data['signdate'] = __date($max_view_data['signdate'],'Y/m/d');
}

# 댓글이 많은글
// $max_cmt_data = $db->get_record('id,signdate,title,view_count', R::$tables[$model->table], "`comment_count`>0 ORDER BY comment_count DESC LIMIT 0,1");
// if(isset($max_cmt_data['id'])){
// 	$max_cmt_data['signdate'] = __date($max_cmt_data['signdate'],'Y/m/d');
// }

# output
out_json(array(
	'result'      =>'true',
	'maxviewdata' =>$max_view_data,
	'msg'         =>''
));
?>
