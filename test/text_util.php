<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;


use Flex\Annona\Text\TextUtil;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# 문자 앞/뒤로 문자 붙이기
$text_value = (new TextUtil( '대한민국' ))->prepend( '반드시' )->append('은 이긴다')->value;
Log::d( $text_value );

# 문자를 지정된길이부터 특정 문자로 변경하기
# 010-0000-7046 => 010-****-7046
# startNumber : 시작위치(0~), endNumber : 길이, chgString : 변형될 문자
$text_value2 = (new TextUtil( $text_value ))->replace(7, strlen($text_value), '*')->value;
Log::d( $text_value2 );

# 글자 자르기
$text_value = (new TextUtil( '대만민국 국제 올림픽 대회에서' ))->cut(10)->value;
Log::d( $text_value );

# 글자 자르기2 문자 줄임표시 문자 추가 안하기
$text_value = (new TextUtil( '대만민국 국제 올림픽 대화에서' ))->cut(10, false)->value;
Log::d( $text_value );

# 글자 자르기3 특정태그 허용
$text_value = (new TextUtil( '<b>대만민국</b> 국제 <font color="red">올림픽</font> 대화에서' ))->cut(10, true, '<b><strong><strike>')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기1 [길이 3 ~ 16], 길이 11
$text_value = (new TextUtil( '01012345678' ))->formatNumberPrintf( '-' )->value;
Log::d( $text_value );

# 특정 숫자 가리기 
$text_value = (new TextUtil( '010-1234-5678' ))->replace(4, 4, '*')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기1 [길이 4 ~ 16] , 길이 8
$text_value = (new TextUtil( '15881234' ))->formatNumberPrintf('-')->value;
Log::d( $text_value );


# 숫자 자동 포멧화 하기3 [길이 4 ~ 16], 길이 16
$text_value = (new TextUtil( '1234567812345678' ))->formatNumberPrintf('-')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기3 [길이 4 ~ 16], 길이 15
$text_value = (new TextUtil( '012345678123456' ))->formatNumberPrintf('-')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기4 [길이 4 ~ 16], 길이 4
$text_value = (new TextUtil( '1000' ))->formatNumberPrintf(',')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기4 [길이 4 ~ 16], 길이 3 숫자 범위 벗어 났을 경우 테스트
$text_value = (new TextUtil( '100' ))->formatNumberPrintf(',')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기4 [길이 4 ~ 16], 길이 5
$text_value = (new TextUtil( '15000' ))->formatNumberPrintf(',')->value;
Log::d( $text_value );

# 숫자 자동 포멧화 하기4 [길이 4 ~ 16], 길이 6
$text_value = (new TextUtil( '153000' ))->formatNumberPrintf(',')->value;
Log::d( $text_value );


# 숫자 자동 포멧화 하고 특정위치 부터까지 특수문자로 표시하기
$text_value = (new TextUtil( '01012345678' ))->formatNumberPrintf('-')->replace(4, 4, '*')->value;
Log::d( $text_value );
?>
