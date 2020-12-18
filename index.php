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

# 암호화 테스트
$cipherEncrypt = new Flex\Cipher\CipherEncrypt('asdfdsdf');
out_ln ( $cipherEncrypt->_md5() );

out_ln(' 암호화 가능한 복호화 ');
$encrypttext = $cipherEncrypt->_base64_urlencode();
out_ln ( $encrypttext );

# 복호화 테스트
$cipherDecrypt = new Flex\Cipher\CipherDecrypt($encrypttext);
out_ln ( '복호화 : '.$cipherDecrypt->_base64_urldecode() );

# 날짜
$dateTimes = new Flex\Date\DateTimes('now');
out_ln ( $dateTimes->wasPassed(1) );
out_ln ( $dateTimes->dateBefore(3) );
out_ln ( $dateTimes->daysAfterDDay() );
out_ln ( $dateTimes->timeLeft24H() );
out_r ( $dateTimes->wkr_args );
?>
