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
// $dateTimez->add(new DateInterval("P1M20DT2H5M30S"));
// $dateTimez->add(new DateInterval("PT2H5M30S"));
$dateTimez->add(new DateInterval("PT2H30M30S"));
$model->end_date = $dateTimez->format('Y-m-d H:i:s');


# 년-월-일 시:분:초
$dateTimezPeriod = new \Flex\Date\DateTimezPeriod($model->timezone);
$period = $dateTimezPeriod->diff($model->start_date, $model->end_date);
Log::d('Total default', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'seconds']);
Log::d('Total 초 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'minutes','nf'=>'1']);
Log::d('Total 분, 소수 1자리 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'hours','nf'=>'3']);
Log::d('Total 시간, 소수 3자리 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'minutes:seconds']);
Log::d('Total 분:초 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'i:s']);
Log::d('Total 분:초 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'hours:minutes:seconds']);
Log::d('Total 시:분:초 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'h:i:s']);
Log::d('Total 시:분:초 ', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'days']);
Log::d('Total 일', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'months','nf'=>'0']);
Log::d('Total 개월, 소수 0자리', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'months:days:hours:minutes:seconds']);
Log::d('Total 월-일 시:분:초', $period);

$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'m-d h:i:s']);
Log::d('Total 월-일 시:분:초', $period);

Log::d ('=======================','=======================');
$period = $dateTimezPeriod->diff($model->start_date, $model->end_date, ["format"=>'top']);

$snsf = explode(' ', $period);
$snsformat = match($snsf[1]) {
    'second','seconds' => sprintf("%d 초전",$snsf[0]),
    'minute','minutes' => sprintf("약%d 분전",$snsf[0]),
    'hour','hours'     => sprintf("약%d 시간전",$snsf[0]),
    'day','days'       => sprintf("약%d 일전",$snsf[0]),
    'month','months'   => sprintf("약%d 개월전",$snsf[0]),
    default            => $model->start_date
};
Log::d('SNS 시간 포멧', $period,'--->',$snsformat);
Log::d ('=======================','=======================');

# 시:분
$dateTimezPeriod2 = new \Flex\Date\DateTimezPeriod($model->timezone);

$period2 = $dateTimezPeriod2->diff('10:11', '11:11', ["format"=>'h:i:s']);
Log::d( '시간 차 11:11 - 10:11 ',$period2);

$period2 = $dateTimezPeriod2->diff('10:11:50', '10:21:40', ["format"=>'i:s']);
Log::d( '분차 10:21:40 - 10:11:50 ', $period2);

Log::d ('=======================','=======================');


# 120초 후
$dateTimez = new \Flex\Date\DateTimez("now", $model->timezone);
$model->start_date = $dateTimez->format('Y-m-d H:i:s');
Log::d('지금시간',$model->start_date);
$dateTimez->modify("120 seconds");
$model->end_date = $dateTimez->format('Y-m-d H:i:s');
Log::d('120초 후',$model->end_date);

$period3 = $dateTimezPeriod2->diff($model->start_date, $model->end_date, ["format"=>'i:s']);
Log::d( '120초 후 시간 차이',$period3);


Log::d ('=======================','=======================');
# 날짜와 날짜 사이 날짜
/**
 * term : 1  // 날짜(1일)간격
 * days : 30 // 며칠(30일/개)
 */
$dateTimezPeriod3 = new \Flex\Date\DateTimezPeriod($model->timezone);
$date_period = $dateTimezPeriod3->period( date('Y-m-d'), ["term"=>1, "days"=>30]);
Log::d ( '시작날짜와 특정기간동안의 날짜 사이의 날짜',$date_period );
?>