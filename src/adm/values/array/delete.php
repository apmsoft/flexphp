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
$form->chkEngNumUnderline('id', 'id', $req->id, true);

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'values'.DIRECTORY_SEPARATOR.'array'.DIRECTORY_SEPARATOR.'config.php';

# 삭제할 수 없는 변수 인지 체크
if(in_array($req->id, $strings_filters)){
	out_json(array('result'=>'false', 'msg_code'=>'w_delete_havenot_permission', 'msg'=>R::$sysmsg['w_delete_havenot_permission']));
}

# Model
$model = new UtilModel();
$model->chkfilename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR."array_"._LANG_.'.json';
$model->filename = (file_exists($model->chkfilename)) ? $model->chkfilename : 'array';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._VALUES_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# resource
R::init(_LANG_);
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# field
unset(R::$r->array[$req->id]);

# ftp 클랙스
$ftp = new Ftp();

# 파일 저장
if($file = $ftp->open_file_read($model->temp_path, $model->real_path))
{
    # 쓰기
    $context = json_encode(R::$r->array,JSON_UNESCAPED_UNICODE);
    if(!$ftp->open_file_write($model->temp_path, $model->real_path, $context)) {
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
    }else{
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
