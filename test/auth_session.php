<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# auth
$authSession = new \Flex\Auth\AuthSession([
    'id'         =>'adm_id',
    'userid'     =>'adm_userid'
]);

# 세션에 추가할 키와 값
$authSession->regiAuth([
    'id'     => 1,
    'userid' => 'apmsoft@gmail.com',
    'level'  => 1
]);

# 세션시작
$authSession->sessionStart();

Log::d('세션값 : ',$_SESSION);
Log::d('세션값이 등록된 값 : ',$authSession->fetch());

# AuthSession 세션값과 _SESSION 세션값 비교 및 체크
Log::d( 'Auth 세션 ID : ',$authSession->id );
Log::d( 'Auth 세션 USERID : ',$authSession->userid );
Log::d( '_SESSION 세션 ID : ',$_SESSION['adm_id'] );
Log::d( '_SESSION 세션 USERID : ',$_SESSION['adm_userid'] );

# 이미 생성된 세션에 세션 키 : 값 추가하기
$authSession->level = 1;

Log::d('세션값이 등록된 값 : ',$authSession->fetch());
Log::d("Auth Level:", $authSession->level );
Log::d("_SESSION Level : ", (isset($_SESSION['level'])? $_SESSION['level']:'') );


# 세션값 비우기
$authSession->unregiAuth();
Log::d('세션 비운 후 : ',$authSession->fetch());
?>
