<?php
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\R;
use Flex\Annona\Request\FormValidation;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

Log::init();
Log::init(Log::MESSAGE_ECHO);

/************************************************
 * Check Validation Filter Options
 * ----------------------------------------------
 * null      : 공백있는지 체크
 * length    : 문자 길이[최소 ~ 최대]
 * disliking : 특수 문자 있으면 reject
 * liking    : 특수 문자 없으면 에러 (최소 1개이상 입력)
 * space     : 공백체크
 * alnum     : 영문 또는 숫자 만
 * repeat    : 연속 반복 문자 체크 [3]
 * number    : 숫자 인지 체크
 * alphabet  : 알파벳 인지 체크
 * upal      : 알파벳 대문자 인지 체크
 * lowal     : 알파벳 소문자 인지 체크
 * firstal   : 첫글자가 알파벳인지 체크
 * jsonf     : json 타입의 데이터인지 체크
 * datef     : 날짜데이터인지 체크
 * timef     : 시간 데이터인지 체크
 * dateperiod: 시작날짜와 종료날짜 이 올바른지 체크
 * equal     : 두 문자가 일치하는지 체크
 * email     : 이메일 데이터인지 체크
 * url       : http:: url 데이터인지 체크
 * floatf    : 소수형 데이터 인지 체크
 ************************************************8*/

# set
$request = new \Flex\Annona\Request\Request();
$request->addr       = '주소 달 서구 대박동 120-4 번지 / 대충빌딩 (2층)';
$request->userid     = 'sfdsafda';
$request->passwd     = 'passwd#';
$request->secure_passwd = 'dsl123safdsa_';
$request->name       = 'DSDnameaaabbb';
$request->phone      = '010-1111-1244';
$request->age        = '100';
$request->eng_str    = 'egd';
$request->email      = 'ddsfd@dfsa.com';
$request->birthday   = '2012-01-03';
$request->start_date = '2012-01-03';
$request->end_date   = '2012-01-04';
$request->authnumber = 'FlexPHP';
$request->point      = '23.7';
$request->key        = 'dsdf_23_7';
// $request->linkurl    = 'http://flexvue.fancyupsoft.com';


try{
    (new FormValidation('addr','주소',$request->addr))->null()->length(4,160)->disliking(['-','/','(',')']);
    (new FormValidation('userid','아이디',$request->userid))->null()->length(4,16)->space()->disliking()->alnum();
    (new FormValidation('passwd', '비밀번호',$request->passwd))->null()->length(4,16)->space()->liking();
    (new FormValidation('name', '이름',$request->name))->null()->length(4,16)->space()->disliking([]);
    (new FormValidation('phone', '전화번호',$request->phone))->null()->length(8,16)->space()->disliking(['-'])->number();
    (new FormValidation('age', '나이',$request->age))->null()->length(1,3)->space()->number();
    (new FormValidation('eng_str', '영어단어',$request->eng_str))->null()->space()->alphabet();
    (new FormValidation('email', '이메일',$request->email))->null()->space()->email();
    (new FormValidation('birthday', '생년-월-일',$request->birthday))->null()->space()->datef();
    (new FormValidation('start_date', '숙박예약',$request->start_date))->null()->dateperiod($request->end_date);
    (new FormValidation('authnumber', '인증번호',$request->authnumber))->null()->space()->equal('FlexPHP');
    (new FormValidation('point', '[소수]값',$request->point))->null()->space()->floatf();
    (new FormValidation('key', '영어+숫자+_ 만허용',$request->key))->null()->space()->disliking(['_'])->alnum();
    (new FormValidation('linkurl', 'http 주소타입인지 체크',$request->linkurl))->space()->url();
}catch(\Exception $e){
    Log::e(json_decode($e->getMessage(),true));
    exit;
}

echo "PASS";
echo PHP_EOL;
?>
