<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;

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

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'values'.DIRECTORY_SEPARATOR.'array'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

$data = array();
if(is_array(R::$r->array)){
	foreach(R::$r->array as $n => $argv){
		$jsondata = strval(json_encode($argv, JSON_UNESCAPED_UNICODE));
		$fstr = substr($jsondata,0,1);
		
		$data_type = 'object';
		if($fstr =='['){
			$data_type = 'array';
		}

		$data[$n] = array(
			'type' => $data_type,
			'value'=> $jsondata
		);
	}
}

# output
out_json(array(
	'result' =>'true',
	'strings_filters' => $strings_filters,
	'msg'    =>$data
));
?>
