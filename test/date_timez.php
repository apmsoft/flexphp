<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;

use Flex\Date\DateTimez;
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# model
$model = new \Flex\Model\Model();
$model->timezone = 'Asia/Seoul';
// $model->timezone = 'UTC';

# 타임존 설정 및 가져오기
// date_default_timezone_set('Asia/Seoul');
// if (date_default_timezone_get()) {
//     $model->timezone = date_default_timezone_get();
// }

// DateTimeZone::ASIA
# timestamp
$dateTimez = new DateTimez(time(), $model->timezone);
Log::d('TIMEZONE ', $dateTimez->timezone );
Log::d('LOCATION ', $dateTimez->location );
Log::d($dateTimez->format('Y-m-d H:i:s'));

Log::d ('=======================');

# now
$datetimev = (new DateTimez("now", $model->timezone))->format('Y-m-d H:i:s P');
Log::d('now', $datetimev );

Log::d ('=======================');

# 오늘
$datetimev = (new DateTimez("today", $model->timezone))->format('Y-m-d H:i:s');
Log::d('오늘', $datetimev );

# 어제
$datetimev = (new DateTimez("yesterday", $model->timezone))->format('Y-m-d H:i:s');
Log::d('어제', $datetimev );

# 내일
$datetimev = (new DateTimez("tomorrow", $model->timezone))->format('Y-m-d H:i:s');
Log::d('내일', $datetimev );

# 120초 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("120 seconds")->format('Y-m-d H:i:s');
Log::d('120초 후', $datetimev );

# 2 일전
$datetimev = (new DateTimez("2 days ago", $model->timezone))->format('Y-m-d H:i:s');
Log::d('2 일전', $datetimev );

# 2 개월 5 일전
$datetimev = (new DateTimez("2 months 5 days ago", $model->timezone))->format('Y-m-d H:i:s');
Log::d('2 개월 5 일전', $datetimev );

# 2 주후
$datetimev = (new DateTimez("2 weeks", $model->timezone))->format('Y-m-d H:i:s');
Log::d('2 주후', $datetimev );


# 15분 경과
$dateTimez = (new DateTimez("now", $model->timezone))->formatter("15 minutes")->format('Y-m-d H:i:s');
Log::d('15분 후', $datetimev );


# modify + 1일 후
$datetimev = (new DateTimez( date('Y-m-d H:i:s'), $model->timezone))->formatter("+1 day")->format('Y/m/d H:i:s');
Log::d('modify 1일 후', $datetimev );

# modify -
$datetimev = (new DateTimez( date('Y-m-d H:i:s'), $model->timezone))->formatter("-3 days")->format('Y/m/d H:i:s');
Log::d('modify 3일전', $datetimev );

Log::d ('=======================');

# 10초 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("PT10S")->format('Y/m/d H:i:s');
Log::d('add 10초 후', $datetimev );

# 10분 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("PT10M")->format('Y/m/d H:i:s');
Log::d('add 10분 후', $datetimev );

# 1시간 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("PT1H")->format('Y/m/d H:i:s');
Log::d('add 한시간 후', $datetimev );

# 1시간 10분 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("PT1H10M")->format('Y/m/d H:i:s');
Log::d('add 한시간 10분 후', $datetimev );

# 하루 1 day 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("P1D")->format('Y/m/d H:i:s');
Log::d('add 하루 후', $datetimev );

# 1주 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("P1W")->format('Y/m/d H:i:s');
Log::d('add 하루 후', $datetimev );

# 한달 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("P1M")->format('Y/m/d H:i:s');
Log::d('add 한달 후', $datetimev );

# 1년 후
$datetimev = (new DateTimez("now", $model->timezone))->formatter("P1Y")->format('Y/m/d H:i:s');
Log::d('add 1년 후', $datetimev );

Log::d ('=======================');

# 10초 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-PT10S")->format('Y/m/d H:i:s');
Log::d('sub 10초 전', $datetimev );

# 10분 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-PT10M")->format('Y/m/d H:i:s');
Log::d('sub 10분 전',$datetimev );

# 1시간 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-PT1H")->format('Y/m/d H:i:s');
Log::d('sub 한시간 전', $datetimev );

# 1시간 10분 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-PT1H10M")->format('Y/m/d H:i:s');
Log::d('sub 한시간 10분 전', $datetimev );

# 하루 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-P1D")->format('Y/m/d H:i:s');
Log::d('sub 하루 전', $datetimev );


# 1주 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-P1W")->format('Y/m/d H:i:s');
Log::d('sub 하루 전', $datetimev );

# 한달 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-P1M")->format('Y/m/d H:i:s');
Log::d('sub 한달 전', $datetimev );

# 1년 전
$datetimev = (new DateTimez("now", $model->timezone))->formatter("-P1Y")->format(DateTime::ATOM);
Log::d('sub 1년 전', 'format ATOM', $datetimev );

# 날짜를 배열화 하기 default
$parse_date = (new DateTimez(date('Y-m-d H:i:s P'), $model->timezone))->parseDate();
Log::d('parse date ', 'default', $parse_date );

# 날짜를 배열화 하기 포멧 정하기
$parse_date = (new DateTimez(date('Y-m-d H:i:s P'), $model->timezone))->parseDate('Y-m-d H:i:s P');
Log::d('parse date', 'format 지정', $parse_date );

# 날짜 증가 또는 이전 시킨 후 데이터 parse 시키기 default
$parse_date = (new DateTimez(date('Y-m-d H:i:s P'), $model->timezone))->formatter("-P1Y")->parseDate();
Log::d('parse date', '날짜 증가 및 이전 시킨 후', 'default', $parse_date );

# 날짜 증가 또는 이전 시킨 후 데이터 parse 시키기 : format 정하기
$parse_date = (new DateTimez(date('Y-m-d H:i:s P'), $model->timezone))->formatter("+P1Y")->parseDate(DateTime::ATOM);
Log::d('parse date', $parse_date );

# 지정된 날짜의 일몰/일출 시간
$sun_info = (new DateTimez(date('Y-m-d'), $model->timezone))->sunInfo();
Log::d('지정 날짜의 일몰/일출 시간', $sun_info );

# 지정된 날짜의 일몰/일출 시간
$sun_info = (new DateTimez(date('Y-m-d'), $model->timezone))->formatter("+10 days")->sunInfo();
Log::d('날짜 변경 후 일몰/일출 시간', $sun_info );

Log::d ('=======================');

# 세계 GMT 기준 시간 알아내기
Log::d('파리  ', (new DateTimez( "now", 'GMT'))->formatter("+1 hours")->format( 'Y-m-d H:i T') );
Log::d('런던  ', (new DateTimez( "now", 'GMT'))->formatter("0 hours")->format( 'Y-m-d H:i T') );
Log::d('뉴욕  ', (new DateTimez( "now", 'GMT'))->formatter("-5 hours")->format( 'Y-m-d H:i T') );
Log::d('두바이', (new DateTimez( "now", 'GMT'))->formatter("+4 hours")->format( 'Y-m-d H:i T') );
Log::d('벤쿠버', (new DateTimez( "now", 'GMT'))->formatter("-10 hours")->format( 'Y-m-d H:i T') );
?>