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
$model->timezone   = 'Asia/Seoul';
$model->start_date = date('Y-m-d H:i:s');
$model->end_date   = '';

# 타임존 설정 및 가져오기
// date_default_timezone_set('Asia/Seoul');
// if (date_default_timezone_get()) {
//     $model->timezone = date_default_timezone_get();
// }


try{
    $dateTimez = new \Flex\Date\DateTimez($model->start_date, $model->timezone);
    // $dateTimez->modify("+2 days");
    $dateTimez->add(new DateInterval("P1M2DT1H"));
    $model->end_date = $dateTimez->format('Y-m-d H:i:s');
}catch(\Exception $e){
    Log::e($e->getMessage());
}


try{
    $dateTimezPeriod = new \Flex\Date\DateTimezPeriod($model->timezone);
    
    // $period = $dateTimezPeriod->getDatePeriod($model->start_date, $model->end_date, ["format"=>'%Y-%M-%D %H:%I:%S']);
    $period = $dateTimezPeriod->getDatePeriod($model->start_date, $model->end_date, ["format"=>'%m month, %d days, %h hours, %i minutes']);
    Log::d( $period);
}catch(\Exception $e){
    Log::e($e->getMessage());
}

?>