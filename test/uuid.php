<?php
use Flex\Annona\Log\Log;


use Flex\Annona\Uuid\UuidGenerator;


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

# create
$v4 = (new UuidGenerator())->v4();
Log::d( 'v4',$v4 );

# uuid, hash 값이 같으면 비교 가능
$v3 = (new UuidGenerator())->v3('0e27f8a6-a4cf-35ac-bf84-bed31526e2c8', 'aa');
Log::d( 'v3',$v3 );

# uuid, hash 값이 같으면 비교 가능
$v5 = (new UuidGenerator())->v5('0e27f8a6-a4cf-35ac-bf84-bed31526e2c8', 'bb');
Log::d( 'v5',$v5 );
?>