<?php
# session_start();
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;
use Flex\Annona\R\R;

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

# 푸시토큰
try{
    $pushFCMMessage = new \Flex\Annona\Push\PushFCMMessage( _FCM_PROJECT_ID_ );
    if($pushFCMMessage->getGoogleAccessToken( _FCM_SERVICE_ACCOUT_KEY_ ))
    {
        $push_title   = 'FCM';
        $push_message = "%s님 flexphp2가 탄생했습니다.";

        # 메세지
        $pushFCMMessage->setDeivce('토큰1', ['title'=>$push_title,'body'=>sprintf($push_message, '홍길동')]);
        $pushFCMMessage->setDeivce('토큰2', ['title'=>$push_title,'body'=>sprintf($push_message, '유관순')]);
        $pushFCMMessage->setDeivce('토큰3', ['title'=>$push_title,'body'=>sprintf($push_message, '이순신')]);

        # 전송
        $pushFCMMessage->send();
    }
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>