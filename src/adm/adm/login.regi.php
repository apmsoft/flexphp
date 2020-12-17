<?php
use Fus3\Util\UtilController;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인상태 체크
if($auth->id){
	out_json(array('result'=>'false', 'msg_code'=>'w_stay_logged_in', 'msg'=>R::$sysmsg['w_stay_logged_in']));
}

# 변수
$req = new Req;
$req->usePOST();

try{
	$controller = new UtilController($req->fetch());
	$controller->on('config');
	$controller->run('admin');
	
	# ip 로그인 체크 /=================
	$allow_iplogin = true;
	if($controller->data['info']['allow_ipall'] == 'n')
	{
		$allow_iplogin = false;

		$allow_ips = array();
		$allow_ips[] = $controller->data['info']['allow_ip1'];
		$allow_ips[] = $controller->data['info']['allow_ip2'];
		$allow_ips[] = $controller->data['info']['allow_ip3'];
		foreach($allow_ips as $ip_addr){
			if($app->ip_address == $ip_addr){
				$allow_iplogin = true;
				break;
			}
		}
	}
	if(!$allow_iplogin){
		out_json(array('result'=>'false','msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
	}

	# 모바일체크
	$allow_mlogin = true;
	if($controller->data['info']['allow_mobile'] == 'n'){
		$allow_mlogin = false;
		if(!$app->is_phone_device){
			$allow_mlogin = true;
		}
	}
	if(!$allow_mlogin){
		out_json(array('result'=>'false','msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
	}

	# 세션키 생성
	$auth_arg =array();
	if(is_array($app['auth'])){
		foreach($app['auth'] as $mk=>$mv){
			$auth_arg[$mv] = $controller->data['info'][$mk];
		}
	}
	$auth->regiAuth($auth_arg);

	# 로그인 세션 ip 정보
	$_SESSION['auth_ip'] = $_SERVER['REMOTE_ADDR'];

	# 관리자 세션 생성
	if(isset($controller->model['superadmin']) && $controller->model['superadmin']){
		# 관리자
		$cipherEncrypt = new CipherEncrypt($controller->data['info']['id'].$_SERVER['REMOTE_ADDR']);
		$_SESSION['aduuid'] = $cipherEncrypt->_md5_utf8encode();
	}else if(isset($_SESSION['aduuid'])){
		unset($_SESSION['aduuid']);
	}

	# output
	out_json($controller->output);
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>
