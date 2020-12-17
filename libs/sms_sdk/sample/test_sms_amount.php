<?php
/* ========[SMS 잔여량 가져오기 ]===========================
* test_sms_send.php : 문자/MMS 보내기용
* test_sms_amount.php : 문자 잔여량 확인용
 ---------------------------------------------------------*/
$sms_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/sms_sdk';
include_once $sms_sdk_root.'/config/config.sms.php';
include_once $sms_sdk_root.'/classes/sms/SmsAmount.class.php';

# 잔여량 가져오기
try{
    $smsAmount = new SmsAmount(_SMS_PROJECTKEY_, _SMS_ID_, _SMS_PASSWD_);
    #$smsAmount->echo_result_print = true;
    $resp = $smsAmount->getRemainingAmount();

    # output
    #@ remaining_count : 잔여량
    echo json_encode(array('result'=>'true', 'remaining_count'=>$resp['remaining_count'], 'msg'=>$resp['msg']));
}catch(Exception $e){
    # output
    echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
	// throw new ErrorException($e->getMessage(),__LINE__);
}
?>