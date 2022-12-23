<?php
use Flex\Annona\Log;


use Flex\Annona\Token\TokenGenerateAtype;


$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# class
$module_id = 'comflexphp';

Log::d("=========================");

# 시크릿키 생성
#$token = (new TokenGenerateAtype( null,10 ))->value;
$token = (new TokenGenerateAtype( $module_id ))->value;
Log::d('module_id ',$token);

Log::d("=========================");

# sha256
$token_256 = (new TokenGenerateAtype( $module_id ))->generateHashKey('sha256')->value;
Log::d('sha256','secret_key',$token_256);

# sha512
$token_512 = (new TokenGenerateAtype( $module_id ))->generateHashKey('sha512')->value;
Log::d('sha512','secret_key',$token_512);

# md5
$token_md5 = (new TokenGenerateAtype( $module_id ))->generateHashKey('md5')->value;
Log::d('md5   ','secret_key',$token_md5);

Log::d("=========================");

# 토큰만들기
$token = (new TokenGenerateAtype( $module_id ))->generateHashKey('sha256')->generateToken(sprintf("%s.",$module_id))->value;
Log::d('EnCrypt :',$token);

# 토큰 디코딩
$token = (new TokenGenerateAtype( $module_id ))->decodeToken($token)->value;
Log::d('DeCrypt :',$token);
Log::d(explode('.', $token));
?>