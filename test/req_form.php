<?php
use Flex\App\App;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본 선언
try{
    $form = \Flex\Req\ReqForm();
}catch(\Exception $e){
    echo $e->getMessage();
}

# test
$req = new \Flex\Req\Req();
$req->useGET();

# set
$req->addr       = '주소';
$req->userid     = 'apdfds dsfd';
$req->passwd     = 'apdfds dsfd';
$req->name       = 'apdfds dsfd';
$req->phone      = '010,1111,1244';
$req->age        = '100';
$req->eng_str    = '영어';
$req->email      = 'ddsfd@dfsa';
$req->birthday   = '2012-01-03';
$req->start_date = '2012-01-03';
$req->end_date   = '2012-01-02';
$req->authnumber = '인증번호';


try{
    $form = \Flex\Req\ReqForm();
    # ( 퀄럼명 , 퀄럼타이틀 , Request 값 , [true : 필수 입력, false : 옵션], [필터옵션 => '필터인자'] )
    $form->chkNull('addr', '주소',$req->addr, true);
    $form->chkUserid('userid', '아이디',$req->userid, true);
    $form->chkPasswd('passwd', '비밀번호',$req->passwd, true);
    $form->chkName('name', '이름',$req->name, true);
    $form->chkPhone('phone', '전화번호',$req->phone, true);
    $form->chkNumber('age', '나이',$req->age, true);
    $form->chkAlphabet('eng_str', '영어단어',$req->eng_str, true);
    $form->chkEmail('email', '이메일',$req->email, true);
    $form->chkDateFormat('birthday', '생년-월-일',$req->birthday, true);
    $form->chkDatePeriod('start_date', '숙박예약기간', [$req->start_date, $req->end_date], true);
    $form->chkEquals('authnumber', '인증번호', [$req->authnumber, 'FlexPHP'], true);
}catch(\Exception $e){
    echo $e->getMessage().PHP_EOL;
    print_r(json_encode($e->getMessage(),true));
}

echo "PASS";
echo PHP_EOL;
?>
