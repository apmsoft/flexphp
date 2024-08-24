<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Cipher\Decrypt;
use Flex\Annona\Cipher\AES256Hash;
use Flex\Annona\Cipher\Base64UrlEncoder;
use Flex\Annona\Cipher\HashEncoder;
use Flex\Annona\Cipher\PasswordHash;


$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_ECHO);

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$random_text = (new \Flex\Annona\Random\Random( ))->_string( 20 );
Log::d("====================================");
Log::d('random_text', $random_text );
Log::d("====================================");

$key = random_bytes(32); // 256 비트 키
$iv = random_bytes(16);  // 128 비트 IV

$aesEncryptor = new Encrypt(new AES256Hash());
$encrypted = $aesEncryptor->process($random_text, $key, $iv);

$aesDecryptor = new Decrypt(new AES256Hash());
$decrypted = $aesDecryptor->process($encrypted, $key, $iv);

Log::d("AES256 Encryption", bin2hex($encrypted));
Log::d("AES256 Decryption", $decrypted);
Log::i("AES256 Result", $decrypted === $random_text ? "PASS" : "FAIL");

// 2. 해시 생성/검증 테스트
$data = "Hash this data";
$hashEncryptor = new Encrypt(new HashEncoder($data));
$hash = $hashEncryptor->process();

$hashDecryptor = new Decrypt(new HashEncoder($data));
$isHashValid = $hashDecryptor->process($hash);

Log::d("Hash Generation", $hash);
Log::d("Hash Verification", $isHashValid);
Log::i("Hash Result", $isHashValid ? "PASS" : "FAIL");
Log::d('');

// 3. 비밀번호 해싱/검증 테스트
$password = "MySecurePassword123!";
$passwordHasher = new Encrypt(new PasswordHash());
$hashedPassword = $passwordHasher->process($password);

$passwordVerifier = new Decrypt(new PasswordHash());
$isPasswordValid = $passwordVerifier->process($password, $hashedPassword);

Log::d("Password Hashing", $hashedPassword);
Log::d("Password Verification", $isPasswordValid);
Log::i("Password Result", $isPasswordValid ? "PASS" : "FAIL");
Log::d('');

// 4. Base64Url 인코딩/디코딩 테스트
$originalData = "This is a test string with special characters: !@#$%^&*()";
$base64UrlEncoder = new Encrypt(new Base64UrlEncoder());
$encoded = $base64UrlEncoder->process($originalData);

$base64UrlDecoder = new Decrypt(new Base64UrlEncoder());
$decoded = $base64UrlDecoder->process($encoded);

Log::d("Base64Url Encoding", $encoded);
Log::d("Base64Url Decoding", $decoded);
Log::i("Base64Url Result", $decoded === $originalData ? "PASS" : "FAIL");
Log::d('');

// 5. 복합 예제: 암호화 -> Base64Url 인코딩 -> 디코딩 -> 복호화
$complexData = "암호화 및 인코딩을 위한 복잡한 데이터";
$complexEncrypted = $aesEncryptor->process($complexData, $key, $iv);
$complexEncoded = $base64UrlEncoder->process($complexEncrypted);

$complexDecoded = $base64UrlDecoder->process($complexEncoded);
$complexDecrypted = $aesDecryptor->process($complexDecoded, $key, $iv);

Log::d("Complex Encryption + Encoding", $complexEncoded);
Log::d("Complex Decoding + Decryption", $complexDecrypted);
Log::i("Complex Result", $complexDecrypted === $complexData ? "PASS" : "FAIL");
?>