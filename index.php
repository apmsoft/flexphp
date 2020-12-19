<?php
use Flex\App\App;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# session
$auth = new Flex\Auth\AuthSession($app['auth']);
$auth->sessionStart();

# out 테스트
echo (isset($_SERVER['HTTP_USER_AGENT']))?? 'nun';
out_ln(App::$version);
out_ln(App::$platform);

# 캘린더 테스트
$calendars = new Flex\Calendars\Calendars(date('Y-m-d'));
$calendars->set_days_of_month();
// out_r($calendars->days_of_month);

# 암호화 테스트
$m5encrypt_text = (new Flex\Cipher\CipherEncrypt('asdfdsdf'))->_md5();
out_ln ( $m5encrypt_text );

out_ln(' 암호화 가능한 복호화 ');
$encrypttext = (new Flex\Cipher\CipherEncrypt('asdfdsdf'))->_base64_urlencode();
out_ln ( $encrypttext );

# 복호화 테스트
$decrypttext = (new Flex\Cipher\CipherDecrypt($encrypttext))->_base64_urldecode();
// out_ln ( '복호화 : '.$cipherDecrypt->_base64_urldecode() );
out_ln ( '복호화 : '.$decrypttext );

# 날짜
$dateTimes = new Flex\Date\DateTimes('now');
out_ln ( $dateTimes->wasPassed(1) );
out_ln ( $dateTimes->dateBefore(3) );
out_ln ( $dateTimes->daysAfterDDay() );
out_ln ( $dateTimes->timeLeft24H() );
out_r ( $dateTimes->wkr_args );

# db where 구문 만들기
$dbHelperWhere = new Flex\Db\DbHelperWhere();
$dbHelperWhere->beginWhereGroup('groupa', 'AND');
$dbHelperWhere->setBuildWhere('name', 'IN' , '홍길동,유관순', true);
$dbHelperWhere->setBuildWhere('age', '>=' , 10, true);
$dbHelperWhere->setBuildWhere('job', 'IN' , ['공무원','프로그래머','경영인','디자이너'], true);
$dbHelperWhere->endWhereGroup();

$dbHelperWhere->beginWhereGroup('groupb', 'OR');
$dbHelperWhere->setBuildWhere('price', 'IN' , [1,2,3,4,5,6], 'OR', true);
$dbHelperWhere->setBuildWhere('price_month', '>=' , 7, 'OR', true);
$dbHelperWhere->endWhereGroup();

out_ln ($dbHelperWhere->where);
?>
