<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.8.2
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 변수
$req = new Req;
$req->useGET();

# 폼 및 request 값 체크
$form = new ReqForm();
$form->chkDateFormat('todate','날짜(년-월-일)', $req->todate, true);

# resource
R::parserResource(_ROOT_PATH_.'/'._RAW_.'/holiday.json', 'holiday');

# 카렌다
$calendar = new Calendars($req->todate);
$calendar->set_memorials(R::$r->holiday['holiday']);
$calendar->set_days_of_month();

# data
$calendarModel = new UtilModel();
$calendarModel->result     ='true';
$calendarModel->this_year  =$calendar->year;
$calendarModel->this_month =$calendar->month;
$calendarModel->this_ymd   =$calendar->year.'-'.$calendar->month.'-'.$calendar->day;
$calendarModel->this_day   =$calendar->day;
$calendarModel->today      =date('Y-m-d',time());
$calendarModel->pre_ymd    =$calendar->get_pre_week_last_date();
$calendarModel->next_ymd   =$calendar->get_next_week_first_date();
$calendarModel->msg        =(isset($calendar->days_of_month[$calendar->daytoweek])) ? $calendar->days_of_month[$calendar->daytoweek] : array();

# output
out_json($calendarModel->fetch());
?>
