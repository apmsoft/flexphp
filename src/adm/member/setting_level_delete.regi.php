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
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ftp.php';

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
$form->chkAlphabet($req->feature, 'feature', $req->feature, true);
$form->chkEngNumUnderline($req->id, 'id', $req->id, true);
$form->chkAlphabet('lang', 'lang', $req->lang, false);

# Model
$model = new UtilModel();
$model->filename = ($req->lang) ? sprintf("array_%s",$req->lang) : 'array';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._VALUES_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->chk_path  = _ROOT_PATH_.DIRECTORY_SEPARATOR._VALUES_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# db 선언 및 접속
// $db = new DbMySqli();

# resource
R::init($req->lang);
R::parserResource($model->chk_path, 'array');
R::parserResourceDefinedID('tables');

# field
if($req->feature == 'field') {
	if(isset(R::$r->array['level'][$req->id])){
    	unset(R::$r->array['level'][$req->id]);
	}
}

# ftp 클랙스
$ftp = new Ftp();

# 파일 저장
if($file = $ftp->open_file_read($model->temp_path, $model->real_path))
{
    # 쓰기
    $context = json_encode(R::$r->array);
    if(!$ftp->open_file_write($model->temp_path, $model->real_path, $context)) {
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
    }else{
    	# adm log
		Log::init(R::$tables['adm_log']);
		Log::d( sprintf("MEMBER LEVEL DELETE LEVEL ID : %d", $req->id) );

    	# output
		out_json(array(
			'result' =>'true',
			'msg'    =>R::$sysmsg['v_delete']
		));
    }
}else{
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}
?>
