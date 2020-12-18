<?php
use Flex\App\App;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# session
$auth = new Flex\Auth\AuthSession($app['auth']);
$auth->sessionStart();

# out 테스트
echo out_ln(App::$version);

# 캘린더 테스트
$calendars = new Flex\Calendars\Calendars(date('Y-m-d'));
$calendars->set_days_of_month();
out_r($calendars->days_of_month);
?>
