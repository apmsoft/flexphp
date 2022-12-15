<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';
#require $path. '/vendor/autoload.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

// Log::options([
//     'datetime'   => true, # 날짜시간 출력여부
//     'debug_type' => true, # 디버그 타입 출력여부
//     'newline'    => true  # 개행문자 출력여부
// ]);

/*************
"require": {
    "google/apiclient": "^2.12"
},
"classmap": [
    "vendor/google/apiclient/src/Google"
]
*/

# define
define('_FCM_SERVICE_ACCOUT_KEY_', '/home/flexphp/public_html/firebase-adminsdk-mnazt-e2d8795ac6.json');
define('_FCM_PROJECT_ID_', 'smartds-5ca46');

# class
$pushFCMMessage = new \Flex\Push\PushFCMMessage( _FCM_PROJECT_ID_ );

# 전송 메세지
$push_send_params = [
    'title'  => 'FlexPHP2',
    'body'   => 'flexphp 가 새롭게 업데이트 되었어요'
];
Log::d('push_send_params ', $push_send_params);

# 푸시토큰
if($pushFCMMessage->getGoogleAccessToken( _FCM_SERVICE_ACCOUT_KEY_ )){
    $pushFCMMessage->setDeivce('토큰1');
    $pushFCMMessage->setDeivce('토큰2');
    #$pushFCMMessage->setDeivces(['토큰1','토큰2']);
    $pushFCMMessage->send($push_send_params);
}
?>