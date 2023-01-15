<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Cipher\Encrypt;

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
$random_text = (new \Flex\Annona\Random\Random( $regs ))->array( 20 );
Log::d("====================================");
Log::d('random_text', $random_text );
Log::d("====================================");


# 암호화
try{
    $_encrypt = (new Encrypt($random_text))->_md5();
    Log::d( $_encrypt );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

$_encrypt = (new Encrypt($random_text))->_md5_base64();
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_md5_utf8encode();
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_hash('sha256');
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_hash('sha512');
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_hash_base64('sha256');
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_hash_base64('sha512');
Log::d( $_encrypt );

$_encrypt = (new Encrypt($random_text))->_base64_urlencode();
Log::d( $_encrypt );
?>