<?php
use Fus3\Date\DateTimes;

# 어떠한 특정날짜에서 일정기간(1일, 3일)기간이 지났는지를 알아보고자 할때
function _date_wasPassed($datetimes, $days){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->wasPassed($days);
}

# 특정날짜를 기준으로 며칠전(1일전, 2일전) 날짜를  알고자 할때
function _date_dateBefore($datetimes, $days){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->dateBefore($days);
}

## 어떠한 특정날짜에서로부터 며칠(3일)뒤의 날짜가 언제인지 알아낸다
function _date_dateAfter($datetimes, $days){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->dateAfter($days);
}

# 오늘 날짜를 기준으로 어떠한 특정날짜에 도달하기 위해 며칠이 남았는지 리턴(1일 남았음, 2일 남았음)
function _date_daysBeforeDDay($datetimes){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->daysBeforeDDay();
}

# 오늘 날자를 기준으로 입력된 날짜가 며칠이나 지났는지 (1일지남, 2일지남)
function _date_daysAfterDDay($datetimes){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->daysAfterDDay();
}

function _date_timeLeft24H($datetimes){
    $dateTimes = new DateTimes($datetimes);
    return $dateTimes->daysAfterDDay();
}
?>