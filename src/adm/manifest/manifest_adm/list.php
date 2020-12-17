<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Dir\DirObject;

# config
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

# model
$model = new UtilModel();
$model->find_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_;

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');

# 실 실행 config 파일이 생성되었는지 체크
if(is_array(R::$manifest)){
	foreach(R::$manifest as $manifid => $manif_args){
		if(isset($manif_args['config'])){
			$_config = $manif_args['config'];

			R::$manifest[$manifid]['config_files'] = array();
			if(is_array($_config)){
				# 생성된 파일이 존재하는지 체크
				$_fdir = $model->find_dir.DIRECTORY_SEPARATOR.$manifid;

				# 생성된 디렉토리가 있는지 체크
				$dirObject = new DirObject($_fdir);
				$config_files = $dirObject->findFiles();

				if(is_array($config_files)){
					foreach($_config as $ck => $cfilename){
						$_cfilename = $cfilename.'.json';
						$is_file = 'false';
						if(array_search($_cfilename, $config_files) > -1){
							$is_file = 'true';
						}

						R::$manifest[$manifid]['config_files'][$ck] = $is_file;
					}
				}
			}
		}
	}
}

# output
out_json(array(
	'result' =>'true',
	'strings_filters' => $strings_filters,
	'msg'    =>R::$manifest
));
?>
