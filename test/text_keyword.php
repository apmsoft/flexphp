<?php
use Flex\App\App;
use Flex\Log\Log;


use Flex\Text\TextKeyword;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# keyword
$query = '대한민국은!!^만만세 하세요&*korean ++/ 100%';
$allow_tags = ['%','+'];

# 키워드 허용된 특수 문자 제외하고 한글 | 영어 추출
$text_value = (new \Flex\Text\TextKeyword( $query, $allow_tags ))->value;
Log::d( $text_value );

### 특정 글자 및 끝글자 삭제 하기 필터 기능 ######
# 특정 글자 삭제
$filter_words =['할수'=>'','있습'=>'','니다'=>'','있나'=>'','있나요'=>'','하세요'=>'','되나'=>'','하는데'=>'','정해져'=>'','이루어'=>'','집니다'=>'',
'이제'=>'','만들'=>'','시켜야'=>'','언제나'=>'','그렇듯'=>'','그래'=>'','그리고'=>'','그러나'=>'','하지만'=>'','시키면'=>'','있는'=>'','처럼'=>'','시킬때'=>'','있다'=>'','정하다'=>'','정해진'=>'','습니다'=>'','보세요'=>''];

# 끝 1글자 삭제
$filter_end_words = [
    '가','이','은','는'
];

$text_value = (new \Flex\Text\TextKeyword( $query, $allow_tags ))->filterCleanWord($filter_words,$filter_end_words)->value;
Log::d($text_value);
?>
