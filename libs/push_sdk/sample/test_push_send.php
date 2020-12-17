<?php
use PushSDK\R\R;
use PushSDK\Push\PushSend;

/* ===========[ PUSH 전송 샘플]============================
 * title  : *제목
 * msg    : *메세지 내용
 * to     : *푸시를 받을 id
 * 
 * test_push_send.php : 푸시 메세지 보내기
 * test_push_amount.php : 푸시 메세지 잔여량
 * test_push_register.php : 푸시 키 등록
 * test_push_history.php : 푸시 전송 이력 가져오기(최대30개)
 ---------------------------------------------------------*/
$push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
include_once $push_sdk_root.'/config/config.push.php';
include_once $push_sdk_root.'/classes/push/PushSend.class.php';
exit;
# 푸시 전송
try{

    # PUSH 푸시 키
    $to = array();
    $to[] = '58b50cbd8d82f759@oe.kr'; // 푸시키 등록 id (회원 식별 번호 또는 아이디)

    # 보내기
    $pushSend = new PushSend(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
    $pushSend->push($to);
    $resp = $pushSend->send(array(
        'title' => '푸시테스트',
        'msg'   => '푸싱 테스트 :'.time()
    ));

    #@ remaining_count : 잔여량
    echo json_encode(array('result'=>'true', 'remaining_count'=>$resp['remaining_count'], 'msg'=>$resp['msg']));
}catch(Exception $e){
    echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
	// throw new ErrorException($e->getMessage(),__LINE__);
}
?>