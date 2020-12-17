<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.8.2
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# use
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Db\DbMySqli;
use Fus3\R\R;

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# resources
R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# 로그아웃시간
$db['logout_time'] = time();
$db->update(R::$tables['admmem'], sprintf("`id`='%s'", $auth->id));

#로그인 세션 destroying
$auth->unregiAuth();

# 로그인 세션 ip 정보
unset($_SESSION['auth_ip']);
unset($_SESSION['aduuid']);

window_location('.'.DIRECTORY_SEPARATOR.'index.php','');
?>
