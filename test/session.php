<?php
# session_start();

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Flex\Annona\Log::init();
Flex\Annona\Log::init(Flex\Annona\Log::MESSAGE_ECHO);

# 세션에 등록할 키와 세션키 설정
$session_types = [
    'id'         =>'adm_id',
    'userid'     =>'adm_userid'
];

# auth
$authSession = new \Flex\Annona\Auth\Session( $session_types );

# 세션에 추가할 키와 값
$authSession->regiAuth([
    'id'     => 1,
    'userid' => 'test@gmail.com',
    'level'  => 1
]);

# 세션시작
$authSession->sessionStart();

Flex\Annona\Log::d('세션값 : ',$_SESSION);
Flex\Annona\Log::d('Auth 클래스 값 : ',$authSession->fetch());

# AuthSession 세션값과 _SESSION 세션값 비교 및 체크
Flex\Annona\Log::d( 'Auth 세션 ID : ',$authSession->adm_id );
Flex\Annona\Log::d( 'Auth 세션 USERID : ',$authSession->adm_userid );
Flex\Annona\Log::d( '_SESSION 세션 ID : ',$_SESSION['adm_id'] );
Flex\Annona\Log::d( '_SESSION 세션 USERID : ',$_SESSION['adm_userid'] );

# 이미 생성된 세션에 세션 키 : 값 추가하기
$authSession->level = 1;

Flex\Annona\Log::d('세션값이 등록된 값 : ',$authSession->fetch());
Flex\Annona\Log::d("Auth Level:", $authSession->level );
Flex\Annona\Log::d("_SESSION Level : ", (isset($_SESSION['level'])? $_SESSION['level']:'') );


# 세션값 비우기
$authSession->unregiAuth();
Flex\Annona\Log::d('세션 비운 후 : ',$authSession->fetch());
?>
