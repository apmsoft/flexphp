<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$stringRandom = new \Flex\String\StringRandom( [] );
Log::d( $stringRandom->arrayRand( 10 ) );

# 랜덤으로 숫자 뽑기 1개 뽑기 : min : 시작범위, max : 끝범위
Log::d( $stringRandom->numberRand( 0, 3000 ) );

# 숫자[0-9] 중 원하는 길이 만큼 랜덤 : 5자리
Log::d( $stringRandom->arrayIntRand( 5 ) );

# 내가 지정한 배열 중에서 랜덤 뽑기 5글자
$stringRandom = new \Flex\String\StringRandom( [
    'a','b',1,5,'f','A','X','B'
] );
Log::d( $stringRandom->arrayRand( 5 ) );
?>
