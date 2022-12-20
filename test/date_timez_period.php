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

# DateTimez
$dateTimez = new \Flex\Date\DateTimez($model->start_date, $model->timezone);
// $dateTimez->modify("+2 days");
$dateTimez->add(new DateInterval("P1M2DT1H5M30S"));
// $dateTimez->add(new DateInterval("PT2H5M30S"));
// $dateTimez->add(new DateInterval("PT1H30M30S"));
$model->end_date = $dateTimez->format('Y-m-d H:i:s');


# 년-월-일 시:분:초
$dateTimezPeriod = new \Flex\Date\DateTimezPeriod($model->timezone);

// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'seconds']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'minutes','nf'=>'1']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'hours','nf'=>'3']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'minutes:seconds']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'i:s']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'hours:minutes:seconds']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'h:i:s']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'days']);
$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'months','nf'=>'0']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'months:days:hours:minutes:seconds']);
// $period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'m-d h:i:s']);
Log::d( $period);


# 시:분
$dateTimezPeriod2 = new \Flex\Date\DateTimezPeriod($model->timezone);
// $period2 = $dateTimezPeriod2->diff('10:11', '11:11', ["format"=>'h:i:s']);
$period2 = $dateTimezPeriod2->diff('10:11:50', '10:21:40', ["format"=>'i:s']);
Log::d( $period2);

# 120초 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$model->start_date = $dateTimez->format('Y-m-d H:i:s');
Log::d('now',$model->start_date);
$dateTimez->modify("120 seconds");
$model->end_date = $dateTimez->format('Y-m-d H:i:s');
Log::d('120초 후',$model->end_date);

$period3 = $dateTimezPeriod2->diff($model->start_date, $model->end_date, ["format"=>'i:s']);
Log::d( '120초 후 시간 차이',$period3);
?>