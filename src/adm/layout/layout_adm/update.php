<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Ftp\Ftp;

# config
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
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('vkey', 'id', $req->vkey, true);
$form->chkNull('filename', '파일경로', $req->filename, true);
$form->chkNull('title', '타이틀', $req->title, true);
$form->chkEngNumUnderline('category', '분류코드', $req->category,false);
$form->chkEngNumUnderline('manifid', 'manifid', $req->manifid,false);

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.'layout_adm'.DIRECTORY_SEPARATOR.'config.php';

# Model
$model = new UtilModel();
$model->chkfilename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR."layout_adm_"._LANG_.'.json';
$model->filename = (file_exists($model->chkfilename)) ? $model->chkfilename : 'layout_adm';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._LAYOUT_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# resource
R::init(_LANG_);
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.'layout_adm.json', 'layout');

# check
if(!isset(R::$layout[$req->vkey])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# field
R::$layout[$req->vkey] = array(
	'filename' => $req->filename,
	'title' => $req->title,
	'category' => $req->category,
	'manifid' => $req->manifid
);

# ftp 클랙스
$ftp = new Ftp();

# 파일 저장
if($file = $ftp->open_file_read($model->temp_path, $model->real_path))
{
    # 쓰기
    $context = json_encode(R::$layout,JSON_UNESCAPED_UNICODE);
    if(!$ftp->open_file_write($model->temp_path, $model->real_path, $context)) {
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
    }else{
    	# output
		out_json(array(
			'result' =>'true',
			'msg'    =>R::$sysmsg['v_modify']
		));
    }
}else{
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}
?>
