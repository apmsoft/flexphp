<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\R\R;
use Fus3\Util\UtilModel;
use Fus3\Auth\AuthSession;

use SMS\Sms\SmsSend;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 1.1
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션시작
$auth = new AuthSession($app['auth']);
$auth->sessionStart();

# sms
$sms_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/sms_sdk';
include_once $sms_sdk_root.'/config/config.sms.php';
include_once $sms_sdk_root.'/classes/sms/SmsSend.class.php';

# 변수
$req = new Req;
$req->usePOST();

# form check
$form = new ReqForm();
$form->chkNull('auth_token', '토큰', $req->auth_token, true);
$form->chkPhone('cellphone', '전화번호', $req->cellphone, true);

# model
$model = new UtilModel();
$model->sms_authno = 0;

# token 비교
if(strcmp($req->auth_token, _AUTHTOKEN_)){
	out_json(array('result'=>'false','msg'=>R::$strings['e_auth_token']));
}

# 랜덤 번호 생성
$model->sms_authno = randomToken(array(1,2,3,4,5,6,7,8,9,1,2,3,4,5,6,7,8,9),6);

# 문자전송
try{

    # SMS 수진 전화번호(숫자로만입력하세요)
    $to = array();
    $to[] = str_replace('-','',$req->cellphone);

    # 보내기
    $smsSend = new SmsSend(_SMS_PROJECTKEY_, _SMS_ID_, _SMS_PASSWD_);
    // $smsSend->echo_result_print = true;
    $smsSend->push($to);
    $resp = $smsSend->send(array(
        'msg'          => '베리핀 휴대폰인증번호 : '.$model->sms_authno,
        'title'        => '',
        'type'         => 'A',
        'reservation'  => 1,
        'mms_filename' => ''
    ));

    # 세션 생성
    $_SESSION['sms_authno'] = $model->sms_authno;

}catch(Exception $e){
    out_json(array('result'=>'false','msg'=>$e->getMessage() ));
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>R::$sysmsg['v_send_sms_auth_number']
));
?>
