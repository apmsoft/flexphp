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
try{
    $dateTimez = new \Flex\Date\DateTimez(time(), $model->timezone);
    Log::d('TIMEZONE ', $dateTimez->timezone);
    Log::d($dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

Log::d ('=======================');

# now
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    Log::d('now',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

Log::d ('=======================');

# today
try{
    $dateTimez = new \Flex\Date\DateTimez("today", $model->timezone);
    Log::d('today',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# yesterday
try{
    $dateTimez = new \Flex\Date\DateTimez("yesterday", $model->timezone);
    Log::d('yesterday',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# tomorrow
try{
    $dateTimez = new \Flex\Date\DateTimez("tomorrow", $model->timezone);
    Log::d('tomorrow',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 2 days ago
try{
    $dateTimez = new \Flex\Date\DateTimez("2 days ago", $model->timezone);
    Log::d('2 days ago',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 2 months 5 days ago
try{
    $dateTimez = new \Flex\Date\DateTimez("2 months 5 days ago", $model->timezone);
    Log::d('2 months 5 days ago',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# +2 weeks
try{
    $dateTimez = new \Flex\Date\DateTimez("2 weeks", $model->timezone);
    Log::d('2 weeks',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 15분 경과
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->modify("15 minutes");
    Log::d('15분 후',$dateTimez->format('Y-m-d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# modify +
try{
    $dateTimez = new \Flex\Date\DateTimez(date('Y-m-d H:i:s'), $model->timezone);
    Log::d('date',$dateTimez->format('Y/m/d H:i:s'));
    $dateTimez->modify("+1 day");
    Log::d('+1 day',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# modify -
try{
    $dateTimez = new \Flex\Date\DateTimez(date('Y-m-d H:i:s'), $model->timezone);
    Log::d('date',$dateTimez->format('Y/m/d H:i:s'));
    $dateTimez->modify("-3 days");
    Log::d('-3 days',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

Log::d ('=======================');

# 10초 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("PT10S"));
    Log::d('10초 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 10분 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("PT10M"));
    Log::d('10분 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1시간 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("PT1H"));
    Log::d('한시간 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1시간 10분 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("PT1H10M"));
    Log::d('한시간 10분 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 하루 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("P1D"));
    Log::d('하루 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1주 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("P1W"));
    Log::d('하루 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 한달 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("P1M"));
    Log::d('한달 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1년 후
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->add(new DateInterval("P1Y"));
    Log::d('1년 후',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

Log::d ('=======================');

# 10초 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("PT10S"));
    Log::d('10초 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 10분 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("PT10M"));
    Log::d('10분 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1시간 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("PT1H"));
    Log::d('한시간 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1시간 10분 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("PT1H10M"));
    Log::d('한시간 10분 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 하루 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("P1D"));
    Log::d('하루 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1주 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("P1W"));
    Log::d('하루 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 한달 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("P1M"));
    Log::d('한달 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 1년 전
try{
    $dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
    $dateTimez->sub(new DateInterval("P1Y"));
    Log::d('1년 전',$dateTimez->format('Y/m/d H:i:s'));
}catch(\Exception $e){
    Log::e($e->getMessage());
}

?>