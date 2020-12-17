<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.8.1
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# use
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Template\Template;
use Fus3\R\R;
use Fus3\Util\UtilUUID;

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if($auth->id){
	# 관리자인지 체크
	if(!_is_null(UtilUUID::make($auth->id,$_SESSION['aduuid']))){
		window_location('.'.DIRECTORY_SEPARATOR.'index.php');
	}
}

# 변수
$req = new Req;
$req->useGET();

if($req->device =='a' && !strcmp($req->auth_token,_AUTHTOKEN_)){
	$_SESSION['device'] = $req->device;
}


# resources
R::parserResourceDefinedID('manifest');
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.'layout_adm.json', 'layout');

# template 선언
try{
	$tpl = new Template(getcwd().DIRECTORY_SEPARATOR.R::$layout['login']['filename']);
}catch(Exception $e){
	throw new ErrorException($e->getMessage(),__LINE__);
}

# tpl 변수
$tpl['strings']         = R::$strings;
// $tpl['menu']			= R::$r->menu;
$tpl['is_apple_device'] = $app->is_apple_device();

# prints
$tpl->compile_dir =_ROOT_PATH_.DIRECTORY_SEPARATOR._TPL_.DIRECTORY_SEPARATOR.'_adm';
#$tpl->compile     = true;
// $tpl->compression = false;
out_html($tpl->display());
?>
