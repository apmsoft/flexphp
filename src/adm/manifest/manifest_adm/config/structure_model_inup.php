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
$form->chkEngNumUnderline('manifid','메니페스트 ID', $req->manifid, true);
$form->chkEngNumUnderline('cfid','실행프로그램 ID', $req->cfid, true);
// $form->chkEngNumUnderline('feature','기능', $req->feature, true);
$form->chkEngNumUnderline('model_field','Model 변수 키', $req->model_field, true);
$form->chkNull('model_value','Model 변수 값', $req->model_value, true);

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# Model
$model = new UtilModel();

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');

# check
if(!isset(R::$manifest[$req->manifid])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# config 있는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if(!$is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

$config_filename = R::$manifest[$req->manifid]['config'][$req->cfid];

# read
R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$req->manifid.'/'.$config_filename.'.json', 'config');

# 같은 기능위치에 같은 기능 퀄럼이 있는지 체크
// if(isset(R::$config[$req->feature][$req->vali_field])){
//     out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
// }

# add
R::$config['model'][$req->model_field] = $req->model_value;

# ftp 클랙스
$ftp = new Ftp();

# resource ftp helper manifest
$utilResFtpHelper2 = new UtilResFtpHelper(_CONFIG_.DIRECTORY_SEPARATOR.$req->manifid, $config_filename);
$utilResFtpHelper2->set_save_server_filename(_CONFIG_.DIRECTORY_SEPARATOR.$req->manifid);

# 파일 저장
if(!$file = $ftp->open_file_read($utilResFtpHelper2->local_filename, $utilResFtpHelper2->server_filename)){
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}

# 쓰기
$context = json_encode(R::$config,JSON_UNESCAPED_UNICODE);
if(!$ftp->open_file_write($utilResFtpHelper2->local_filename, $utilResFtpHelper2->save_server_filename, $context)) {
    out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>R::$sysmsg['v_update']
));
?>
