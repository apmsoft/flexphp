<?php
use SMS\Sms\SmsSend;
// use PushSDK\R\R;
/* ===========[ SMS 전송 샘플]============================

 * msg          : *메세지 내용
 * title        : LMS 사용시 제목(꼭 입력할 필요는 없음)
 * type         : A : SMS만 허용(80바이트 넘으면 수신 불가) , C : LMS 허용, D : MMS 허용
 * reservation  : 1 (즉시발송), yyyy-MM-dd H:i:s (예약발송)
 * mms_filename : 서버이미지 절대경로($_SERVER['DOCUMENT_ROOT']/mms/image/test.jpg) - MMS 첨부이미지(jpg), 1MByte 이하, 첨부이미지 풀경로 , 가로(300px) x 세로(400px)
 * 
 * test_sms_send.php : 문자/MMS 보내기용
 * test_sms_amount.php : 문자 잔여량 확인용
 ---------------------------------------------------------*/
$sms_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/sms_sdk';
include_once $sms_sdk_root.'/config/config.sms.php';
include_once $sms_sdk_root.'/classes/sms/SmsSend.class.php';
exit;
# 문자전소
try{

    # SMS 수진 전화번호(숫자로만입력하세요)
    $to = array();
    $to[] = '01040237046'; // 01011110000
    $to[] = '01066334977'; // 01022221111

    # 보내기
    $smsSend = new SmsSend(_SMS_PROJECTKEY_, _SMS_ID_, _SMS_PASSWD_);
    // $smsSend->echo_result_print = true;
    $smsSend->push($to);
    $resp = $smsSend->send(array(
        'msg'          => '수소차 테스트',
        'title'        => '',
        'type'         => 'A',
        'reservation'  => 1,
        'mms_filename' => ''//$path.'/assets/applepie/images/mms_file.jpg'
    ));

    #@ remaining_count : 잔여량
    echo json_encode(array('result'=>'true', 'remaining_count'=>$resp['remaining_count'], 'msg'=>$resp['msg']));
}catch(Exception $e){
    echo json_encode(array('result'=>'false','msg'=>$e->getMessage() ));
	// throw new ErrorException($e->getMessage(),__LINE__);
}
?>