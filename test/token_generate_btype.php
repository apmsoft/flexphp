<?php
use Flex\Annona\Log;

use Flex\Annona\Token\TokenGenerateBtype;

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

// $generate_string = 'abdsrefdsfds';
$generate_string = null;

# 랜덤 문자 생성 : 토큰 비교 불가
$token = (new TokenGenerateBtype($generate_string,10))->value;
Log::d($token);

# generate HashKey
$token = (new TokenGenerateBtype($generate_string,10))->generateHashKey()->value;
Log::d($token);

# generate Token
$token = (new TokenGenerateBtype($generate_string,10))->generateHashKey()->generateToken()->value;
Log::d($token);
?>