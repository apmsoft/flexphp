<?php
use Flex\Annona\App;
use Flex\Annona\Log;


use Flex\Annona\Random\Random;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$randomv = (new Random( [] ))->arrayRand( 10 );
Log::d( $randomv );

# 랜덤으로 숫자 뽑기 1개 뽑기 : min : 시작범위, max : 끝범위
$randomv = (new Random( ))->numberRand( 0, 3000 );
Log::d( $randomv );

# 숫자[0-9] 중 원하는 길이 만큼 랜덤 : 5자리
$randomv = (new Random( [0,1,2,3,4,5,6,7,8,9] ))->arrayIntRand( 5 );
Log::d( $randomv );

# 내가 지정한 배열 중에서 랜덤 뽑기 5글자
$regs = [
    'A',1,'B',2,'C',3,'D',4,'E',5,'F',6,'G',7,'H',8,'A',9,'J',9,
    'K',1,'L',2,'M',3,'N',4,'A',5,'P',6,'Q',7,'R',8,'S',9,'T',7,
    'U',1,'V',2,'X',3,'Y',4,'Z','!','@','#','$','*','^','%'
];

$randomv = (new Random( $regs ))->arrayRand( 10 );
Log::d( $randomv );
?>
