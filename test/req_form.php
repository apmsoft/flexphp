<?php
use Flex\App\App;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# test
$req = new \Flex\Req\Req();
$req->useGET();

/************************************************
 * ***** [ Check Validation Filter Options] *****
 * CLASS : ReqStrChecker.class
 * ----------------------------------------------
 * isSpace                              :공백있는지 체크
 * isStringLength => [3,30]             :문자 길이[최소 ~ 최대]
 * isKorean                             :한글인지체크
 * isEtcString => ['-','/']             :허용할 특수 문자
 * isSameRepeatString => [3]            :같은문자가 몇번이상 연속사용되는지 체크
 * isNumber                             :숫자인지 체크
 * isAlphabet                           :영어인지 체크
 * isUpAlphabet                         :영문 대문자 인지 체크
 * isLowAlphabet                        :영문 소문자 인지 체크
 * isFirstAlphabet                      :첫번째 문자가 영문인지 체크
 * isJSON                               :데이터가 json 타입인지 체크
 * chkDate                              :날짜형식이 정확한지 체크
 * chkTime                              :시간이 정확한지 체크
 * chkDatePeriod                        :날짜 기간 (시작 ~ 끝)이 올바른지 체크
 * equals                               :두 문자가 일치하는지 체크
 ************************************************8*/

/**
  * 에러메세지 위치 : res/values/sysmsg.json
  * "e_null": "을(를) 입력 하세요",
  * "e_spaces": "공백없이 입력 하세요",
  * "e_same_repeat_string" : "연속된 문자를 %s자 이상 입력할 수 없습니다.",
  * "e_number": "숫자만 입력하세요",
  * "e_korean": "한글을 입력할 수 없습니다",
  * "e_string_length": "길이는 %d~%d자를 입력하세요",
  * "e_etc_string": "허용된 특수문자%s 외에는 입력할 수 없습니다",
  * "e_alphabet": "영어(alphabet)을 입력 하세요",
  * "e_date": "날짜를 정확하게 입력 하세요",
  * "e_time": "시간을 정확하게 입력 하세요",
  * "e_date_period": "날짜 기간을 정확하게 입력 하세요",
  * "e_equals": "일치하지 않습니다.",
  * "e_up_alphabet": "대문자로 입력 하세요",
  * "e_low_alphabet": "소문자로 입력 하세요",
  * "e_first_alphabet":"첫 글자는 영문으로만 입력 하세요",
  * "e_json":"데이터를 JSON 형태로 입력 하세요",
  * "e_float": "숫자와 소수형 숫자만 입력하세요",
  * "e_link_url": "URL 주소 정확하게 입력 하세요",
  * "e_password_secure_symbol": "특수문자(_,$,#,!,^,*,-,@,&,(,),+)를 최소 한자 이상 입력하세요.",
  */

# set
$req->addr       = '주소 달 서구 대박동 120-4 번지 / 대충빌딩 (2층) ';
$req->userid     = 'useriddsfd';
$req->passwd     = 'passwd';
$req->secure_passwd = 'dsl123safdsa';
$req->name       = 'DSDnameaaabbb';
$req->phone      = '010-1111-1244';
$req->age        = '100';
$req->eng_str    = 'egd';
$req->email      = 'ddsfd@dfsa.com';
$req->birthday   = '2012-01-03';
$req->start_date = '2012-01-03';
$req->end_date   = '2012-01-04';
$req->authnumber = 'FlexPHP';
$req->point      = '23.7';
$req->key        = 'dsdf_23_7';
$req->linkurl    = 'http://flexvue.fancyupsoft.com';


try{
    $form = new \Flex\Req\ReqForm();
    # ( 퀄럼명 , 퀄럼타이틀 , Request 값 , [true : 필수 입력, false : 옵션], [필터옵션 => '필터인자'] )
    $form->chkNull('addr', '주소',$req->addr, true,['isStringLength'=>[4,160],'isEtcString'=> ['-','/','(',')']]);
    $form->chkUserid('userid', '아이디',$req->userid, true,['isSpace','isStringLength'=>[4,16],'isKorean']);
    $form->chkPasswd('passwd', '비밀번호',$req->passwd, true);
    $form->chkPasswdSecure('secure_passwd', '암호화특수문자 허용범위 ',$req->secure_passwd, true);
    $form->chkName('name', '이름',$req->name, true);
    $form->chkPhone('phone', '전화번호',$req->phone, true);
    $form->chkNumber('age', '나이',$req->age, true);
    $form->chkAlphabet('eng_str', '영어단어',$req->eng_str, true);
    $form->chkEmail('email', '이메일',$req->email, true);
    $form->chkDateFormat('birthday', '생년-월-일',$req->birthday, true);
    $form->chkDatePeriod('start_date', '숙박예약', [$req->start_date, $req->end_date], true);
    $form->chkEquals('authnumber', '인증번호', [$req->authnumber, 'FlexPHP'], true);
    $form->chkFloat('point', '[자연수|소수]범위체크', $req->point, true);
    $form->chkEngNumUnderline('key', '[영어+숫자+_]만으로 된문자인지 체크', $req->key, true);
    $form->chkLinkurl('linkurl', 'http 주소타입인지 체크', $req->linkurl, true);
}catch(\Exception $e){
    echo $e->getMessage().PHP_EOL;
    print_r(json_encode($e->getMessage(),true));
    exit;
}

echo "PASS";
echo PHP_EOL;
?>
