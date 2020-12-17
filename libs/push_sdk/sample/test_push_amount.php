<?php
use PushSDK\R\R;
use PushSDK\Push\PushAmount;

/* ===========[ PUSH 잔여량 샘플]============================
 * test_push_send.php : 푸시 메세지 보내기
 * test_push_amount.php : 푸시 메세지 잔여량
 * test_push_register.php : 푸시 키 등록
 * test_push_history.php : 푸시 전송 이력 가져오기(최대30개)
 ---------------------------------------------------------*/
$push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
include_once $push_sdk_root.'/config/config.push.php';
include_once $push_sdk_root.'/classes/push/PushAmount.class.php';

# 잔여량 가져오기
try{
    $pushAmount = new PushAmount(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
    $resp = $pushAmount->getRemainingAmount();

    # output
    #@ remaining_count : 잔여량
    echo json_encode(array('result'=>'true', 'remaining_count'=>$resp['remaining_count'], 'msg'=>$resp['msg']));
}catch(Exception $e){
    # output
    echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
	// throw new ErrorException($e->getMessage(),__LINE__);
}
?>