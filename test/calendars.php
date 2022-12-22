<?php
use Flex\App\App;
use Flex\Log\Log;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# model
$model = new \Flex\Model\Model();
$model->todate = date('Y-m-d');


# resource
R::parser(_ROOT_PATH_.'/'._RAW_.'/holiday.json', 'holiday');

# calendar
$calendar = new \Flex\Calendars\Calendars($model->todate);
$calendar->set_memorials( R::$r->holiday[R::$language] );
$calendar->set_days_of_month();
$months =$calendar->days_of_month;

Log::d('-------------');
Log::d('year', $calendar->year);
Log::d('month', $calendar->month);
Log::d('lastday', $calendar->lastday);
Log::d('lastdaydow', $calendar->lastdaydow);
Log::d('firstdaydow', $calendar->firstdaydow);
Log::d('monthname', $calendar->monthname);
Log::d('shortmonthname', $calendar->shortmonthname);

Log::d('-------------');
Log::d( $calendar->get_zodiac_sign(),'띠');
Log::d( $calendar->get_sexagenary_cycle(),'년');

Log::d('-------------');
Log::d('day', $calendar->day);
Log::d('daydow', $calendar->daydow);
Log::d('dayname', $calendar->dayname);
Log::d('shortdayname', $calendar->shortdayname);

Log::d('-------------');
Log::d('cur_week', $calendar->cur_week);
Log::d('pre_week', $calendar->pre_week);
Log::d('next_week', $calendar->next_week);

Log::d('-------------');
Log::d('pre_year', $calendar->pre_year);
Log::d('pre_month', $calendar->pre_month);

Log::d('-------------');
Log::d('next_year', $calendar->next_year);
Log::d('next_month', $calendar->next_month);

Log::d('-----Calendar--------');
Log::d($months);
?>