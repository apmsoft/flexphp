<?php
use PushSDK\R\R;
use PushSDK\Push\PushRegister;

/* ===========[ PUSH 키 등록 샘플]============================
 * push_token  : * 푸시토큰
 * os_type     : * 안드로이드[a], iOS[i]
 * id          : * 푸시키 식별번호 및 문자 ( 회원 번호 또는 아이디 사용 하세요 )
 * 
 * test_push_send.php : 푸시 메세지 보내기
 * test_push_amount.php : 푸시 메세지 잔여량
 * test_push_register.php : 푸시 키 등록
 * test_push_history.php : 푸시 전송 이력 가져오기(최대30개)
 ---------------------------------------------------------*/
 $push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
 include_once $push_sdk_root.'/config/config.push.php';
 include_once $push_sdk_root.'/classes/push/PushRegister.class.php';
 
 # 푸시 토큰 등록
try{
    # PUSH 푸시 키
    $push_token = 'uFiSUAv3V4G9EPGsuhOVRcn7vwXxXrq-3YyHiPOqo5qgvzfsIVEfqj-sample-data';

    # 푸시 키 등록하기
    $PushRegister = new PushRegister(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
    $PushRegister->push($push_token);
    $resp = $PushRegister->send(array(
        'os_type' => 'a',   // [필수] 안드로이드 : a, iOS : i
        'id'      => 1      // [필수][중복불가] 회원식별 아이디 또는 번호 [String | Integer] [test@test.com | 1] 중복되지 않도록 하세요.
    ));

    #@ 성공
    echo json_encode(array('result'=>'true', 'msg'=>$resp['msg']));
}catch(Exception $e){
    // echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
    throw new ErrorException('error :  '.$e->getMessage(),__LINE__);
}
?>