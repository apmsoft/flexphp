<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;

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

# 타임존 설정 및 가져오기
// date_default_timezone_set('Asia/Seoul');
// if (date_default_timezone_get()) {
//     $model->timezone = date_default_timezone_get();
// }

// DateTimeZone::ASIA
# timestamp
$dateTimez = new \Flex\Date\DateTimez(time(), $model->timezone);
Log::d('TIMEZONE ', $dateTimez->timezone);
Log::d('LOCATION ', $dateTimez->location);
Log::d($dateTimez->format('Y-m-d H:i:s'));

Log::d ('=======================');

# now
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
Log::d('now',$dateTimez->format('Y-m-d H:i:s P'));

Log::d ('=======================');

# 오늘
$dateTimez = new \Flex\Date\DateTimez("today", $model->timezone);
Log::d('오늘',$dateTimez->format('Y-m-d H:i:s'));

# 어제
$dateTimez = new \Flex\Date\DateTimez("yesterday", $model->timezone);
Log::d('어제',$dateTimez->format('Y-m-d H:i:s'));

# 내일
$dateTimez = new \Flex\Date\DateTimez("tomorrow", $model->timezone);
Log::d('내일',$dateTimez->format('Y-m-d H:i:s'));

# 120초 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
Log::d('120초 후',$dateTimez->formatter("120 seconds")->format('Y-m-d H:i:s'));

# 2 일전
$dateTimez = new \Flex\Date\DateTimez("2 days ago", $model->timezone);
Log::d('2 일전',$dateTimez->format('Y-m-d H:i:s'));

# 2 개월 5 일전
$dateTimez = new \Flex\Date\DateTimez("2 months 5 days ago", $model->timezone);
$datetimev = $dateTimez->format('Y-m-d H:i:s');
Log::d('2 개월 5 일전', $datetimev );

# 2 주후
$dateTimez = new \Flex\Date\DateTimez("2 weeks", $model->timezone);
$datetimev = $dateTimez->format('Y-m-d H:i:s');
Log::d('2 주후', $datetimev );


# 15분 경과
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("15 minutes")->format('Y-m-d H:i:s');
Log::d('15분 후', $datetimev );


# modify + 1일 후
$dateTimez = new \Flex\Date\DateTimez(date('Y-m-d H:i:s'), $model->timezone);
Log::d('date',$dateTimez->format('Y/m/d H:i:s'));
$datetimev = $dateTimez->formatter("+1 day")->format('Y/m/d H:i:s');
Log::d('modify 1일 후', $datetimev );

# modify -
$dateTimez = new \Flex\Date\DateTimez(date('Y-m-d H:i:s'), $model->timezone);
Log::d('date',$dateTimez->format('Y/m/d H:i:s'));

$datetimev = $dateTimez->formatter("-3 days")->format('Y/m/d H:i:s');
Log::d('modify 3일전', $datetimev );

Log::d ('=======================');

# 10초 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("PT10S")->format('Y/m/d H:i:s');
Log::d('add 10초 후', $datetimev );

# 10분 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("PT10M")->format('Y/m/d H:i:s');
Log::d('add 10분 후', $datetimev );

# 1시간 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("PT1H")->format('Y/m/d H:i:s');
Log::d('add 한시간 후', $datetimev );

# 1시간 10분 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("PT1H10M")->format('Y/m/d H:i:s');
Log::d('add 한시간 10분 후', $datetimev );

# 하루 1 day 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("P1D")->format('Y/m/d H:i:s');
Log::d('add 하루 후', $datetimev );

# 1주 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("P1W")->format('Y/m/d H:i:s');
Log::d('add 하루 후', $datetimev );

# 한달 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("P1M")->format('Y/m/d H:i:s');
Log::d('add 한달 후', $datetimev );

# 1년 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("P1Y")->format('Y/m/d H:i:s');
Log::d('add 1년 후', $datetimev );

Log::d ('=======================');

# 10초 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-PT10S")->format('Y/m/d H:i:s');
Log::d('sub 10초 전', $datetimev );

# 10분 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-PT10M")->format('Y/m/d H:i:s');
Log::d('sub 10분 전',$datetimev );

# 1시간 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-PT1H")->format('Y/m/d H:i:s');
Log::d('sub 한시간 전', $datetimev );

# 1시간 10분 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-PT1H10M")->format('Y/m/d H:i:s');
Log::d('sub 한시간 10분 전', $datetimev );

# 하루 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev =  $dateTimez->formatter("-P1D")->format('Y/m/d H:i:s');
Log::d('sub 하루 전', $datetimev );


# 1주 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-P1W")->format('Y/m/d H:i:s');
Log::d('sub 하루 전', $datetimev );

# 한달 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-P1M")->format('Y/m/d H:i:s');
Log::d('sub 한달 전', $datetimev );

# 1년 전
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$datetimev = $dateTimez->formatter("-P1Y")->format('Y/m/d H:i:s');
Log::d('sub 1년 전', $datetimev );

?>