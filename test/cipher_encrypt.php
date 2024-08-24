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

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$random_text = (new \Flex\Annona\Random\Random( ))->_string( 20 );
Log::d("====================================");
Log::d('random_text', $random_text );
Log::d("====================================");


# 암호화
try{
    $_encrypt = (new Encrypt($random_text))->_hash('sha256');
    Log::d( $_encrypt );
}catch(\Exception $e){
    Log::e($e->getMessage());
}


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

# aes 256
$secret_key = "testdd";
$secret_iv = "ivddd";
$_encrypt = (new Encrypt("23D46G899K12M45TU3#%"))->_aes256_encrypt($secret_key,$secret_iv);
Log::d( 'aes encrypt',$_encrypt );
?>