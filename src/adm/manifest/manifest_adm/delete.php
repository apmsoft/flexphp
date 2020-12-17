<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Ftp\Ftp;
use Fus3\Util\UtilResFtpHelper;
use Fus3\Dir\DirObject;

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
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config.php';

# 삭제할 수 없는 변수 인지 체크
if(in_array($req->id, $strings_filters)){
	out_json(array('result'=>'false', 'msg_code'=>'w_delete_havenot_permission', 'msg'=>R::$sysmsg['w_delete_havenot_permission']));
}

# Model
$model = new UtilModel();

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');

# check
if(!isset(R::$manifest[$req->id])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# field
unset(R::$manifest[$req->id]);

# ftp 클랙스
$ftp = new Ftp();

# 기존에 있는 디렉토리 및 파일 삭제
$server_dir = _CONFIG_.DIRECTORY_SEPARATOR.$req->id;
$dirObject = new DirObject(_ROOT_PATH_.DIRECTORY_SEPARATOR.$server_dir);
if($dirObject->isDir(_ROOT_PATH_.DIRECTORY_SEPARATOR.$server_dir)){
	$config_files = $dirObject->findFiles();
	if(is_array($config_files))
	{
		# 파일삭제
		foreach($config_files as $fn=> $fname){
			if(!$ftp->ftp_delete(_FTP_DIR_.$server_dir.DIRECTORY_SEPARATOR.$fname)){
				out_json(array('result'=>'false', 'msg'=>'file delete failed!'));
				break;
			}
		}

		#디렉토리 삭제
		if(!$ftp->ftp_rmdir(_FTP_DIR_.$server_dir)){
			out_json(array('result'=>'false', 'msg'=>'directory rmdir failed!'));
		}

		# layout docs 문서가 있는지 체크
		if(file_exists(_ROOT_PATH_.'/'._LAYOUT_.'/docs/'.$req->id.'.json')){
			if(!$ftp->ftp_delete(_FTP_DIR_._LAYOUT_.'/docs/'.$req->id.'.json')){
				out_json(array('result'=>'false', 'msg'=>'file delete failed!'));
			}
		}
	}
}

# resource ftp helper manifest
$utilResFtpHelper2 = new UtilResFtpHelper(_RES_, 'manifest_adm');
$utilResFtpHelper2->set_save_server_filename(_RES_);

# 파일 저장
if(!$file = $ftp->open_file_read($utilResFtpHelper2->local_filename, $utilResFtpHelper2->server_filename)){
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}

# 쓰기
$context = json_encode(R::$manifest,JSON_UNESCAPED_UNICODE);
if(!$ftp->open_file_write($utilResFtpHelper2->local_filename, $utilResFtpHelper2->save_server_filename, $context)) {
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>R::$sysmsg['v_delete']
));
?>
