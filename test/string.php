<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# 기본 글자
$str = 'Victory';

#
$stringUtil = new \Flex\String\StringUtil( $str );
Log::d( $stringUtil->str );

# 앞에 문자 덮붙이기
$stringUtil->prepend( '대한민국');
Log::d( $stringUtil->str );

# 뒤에 문자 붙이기
$stringUtil->append( ' VS 브라질');
Log::d( $stringUtil->str );

# 문자를 지정된길이부터 특정 문자로 변경하기
# 010-0000-7046 => 010-****-7046
# startNumber : 시작위치(0~), endNumber : 길이, chgString : 변형될 문자
$stringUtil->replace(7, 3, '*');
Log::d( $stringUtil->str );

# 글자 자르기
$stringUtil2 = new \Flex\String\StringUtil( '대만민국 국제 올림픽 대화에서' );
$stringUtil2->cut(10);
Log::d( $stringUtil2->str );

# 글자 자르기2 문자 줄임표시 문자 추가 안하기
$stringUtil3 = new \Flex\String\StringUtil( '대만민국 국제 올림픽 대화에서' );
$stringUtil3->cut(10, false);
Log::d( $stringUtil3->str );

# 글자 자르기3 특정태그 허용
$stringUtil4 = new \Flex\String\StringUtil( '<b>대만민국</b> 국제 <font color="red">올림픽</font> 대화에서' );
$stringUtil4->cut(80, true, '<font><strong><b><strike>');
Log::d( $stringUtil4->str );

# 숫자 자동 포멧화 하기1 [길이 7 ~ 16], 길이 11
$stringUtil6 = new \Flex\String\StringUtil( '01012345678' );
$stringUtil6->formatNumberPrintf( '-');
Log::d( $stringUtil6->str );

# 특정 숫자 가리기 
$stringUtil61 = new \Flex\String\StringUtil( $stringUtil6->str );
# 시작 index, 길이만큼, 대체할문자
$stringUtil61->replace(4, 4, '*');
Log::d( $stringUtil61->str );

# 숫자 자동 포멧화 하기1 [길이 7 ~ 16] , 길이 8
$stringUtil7 = new \Flex\String\StringUtil( '15881234' );
$stringUtil7->formatNumberPrintf( '-');
Log::d( $stringUtil7->str );


# 숫자 자동 포멧화 하기3 [길이 7 ~ 16], 길이 16
$stringUtil8 = new \Flex\String\StringUtil( '1234567812345678' );
$stringUtil8->formatNumberPrintf( '-');
Log::d( $stringUtil8->str );

# 숫자 자동 포멧화 하기3 [길이 7 ~ 16], 길이 15
$stringUtil9 = new \Flex\String\StringUtil( '012345678123456' );
$stringUtil9->formatNumberPrintf( '-');
Log::d( $stringUtil9->str );


?>
