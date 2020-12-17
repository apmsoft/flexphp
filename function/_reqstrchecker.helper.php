<?php
/** ======================================================
| @UPDATE   : 2014-11-25
# purpose : 문자를 체크클래스 함수 제공
# 클래스와 기능이 동일
# ReqStrChecker.class.php
----------------------------------------------------------*/
use Fus3\Req\ReqStrChecker;

function _is_null($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isNull();
}

function _is_space($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isSpace();
}

function _is_same_repeat_string($value, $max=3){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isSameRepeatString();
}

function _is_number($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isNumber();
}

function _is_alphabet($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isAlphabet();
}

function _is_up_alphabet($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isUpAlphabet();
}

function _is_low_alphabet($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isLowAlphabet();
}

function _is_korean($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isKorean();
}

# allow = "-,_"; 허용시킬
function _is_etc_string($value, $allow=''){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isEtcString($allow);
}

function _is_first_alphabet($value){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isFirstAlphabet();
}

function _is_string_length($value,$min,$max){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->isStringLength($min,$max);
}

function _chk_date($value,$min,$max){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->chkDate($min,$max);
}

function _chk_date_period($value,$min,$max){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->chkDatePeriod($min,$max);
}

function _equals($value,$s){
    $isChceker = new ReqStrChecker($value);
    return $isChceker->equals($s);
}
?>