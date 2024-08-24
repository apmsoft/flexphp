<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Cipher\Decrypt;

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

$regs = [
    'A',1,'B',2,'C',3,'D',4,'E',5,'F',6,'G',7,'H',8,'A',9,'J',9,
    'K',1,'L',2,'M',3,'N',4,'A',5,'P',6,'Q',7,'R',8,'S',9,'T',7,
    'U',1,'V',2,'X',3,'Y',4,'Z','!','@','#','$','*','^','%'
];

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$random_text = (new \Flex\Annona\Random\Random( $regs ))->_string( 20 );
Log::d("====================================");
Log::d('random_text', $random_text );
Log::d("====================================");


try{
    # 암호화
    $_encrypt = (new Encrypt($random_text))->_base64_urlencode();
    Log::d( '암호화',$_encrypt );

    # 복호화 ===========================
    $_decrypt = (new Decrypt($_encrypt))->_base64_urldecode();
    Log::d( '복호화',$_decrypt );

    # aes 복호화
    $secret_key = "testdd";
    $secret_iv = "ivddd";
    $_decrypt = (new Decrypt("90DrvosFq7IUXbX2+KR48pyb2INGg58EDHozHpu/RJ4="))->_aes256_decrypt($secret_key,$secret_iv);
    Log::d( 'aes 복호화',$_decrypt );
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>