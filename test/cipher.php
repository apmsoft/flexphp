<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Cipher\CipherGeneric;
use Flex\Annona\Cipher\AES256Hash;
use Flex\Annona\Cipher\Base64UrlEncoder;
use Flex\Annona\Cipher\HashEncoder;
use Flex\Annona\Cipher\PasswordHash;
use Flex\Annona\Cipher\ROT13Encoder;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_ECHO);

# 배열 중에서 랜덤 뽑기 10글자 : 기본 배열값중에서
$random_text = (new \Flex\Annona\Random\Random())->_string(20);
Log::d("====================================");
Log::d('random_text', $random_text);
Log::d("====================================");

$key = random_bytes(32); // 256 비트 키
$iv = random_bytes(16);  // 128 비트 IV
Log::d(bin2hex($key), bin2hex($iv));

// 1. AES256 암호화/복호화 테스트
$aesCipher = new CipherGeneric(new AES256Hash());
$encrypted = $aesCipher->encrypt($random_text, $key, $iv);
$decrypted = $aesCipher->decrypt($encrypted, $key, $iv);

Log::d("AES256 Encryption", bin2hex($encrypted));
Log::d("AES256 Decryption", $decrypted);
Log::i("AES256 Result", $decrypted === $random_text ? "PASS" : "FAIL");
Log::d('');

// 2. 해시 생성/검증 테스트
$hash = (new CipherGeneric(new HashEncoder($random_text)))->hash();

$hashVerifier = new CipherGeneric(new HashEncoder($random_text));
$isHashValid = hash_equals($hash, $hashVerifier->hash());

Log::d("Hash Generation", $hash);
Log::d("Hash Verification", $isHashValid);
Log::i("Hash Result", $isHashValid ? "PASS" : "FAIL");
Log::d('');

// 3. 비밀번호 해싱/검증 테스트
$passwordCipher = new CipherGeneric(new PasswordHash());
$hashedPassword = $passwordCipher->hash($random_text);

$isPasswordValid = $passwordCipher->verify($random_text, $hashedPassword);

Log::d("Password Hashing", $hashedPassword);
Log::d("Password Verification", $isPasswordValid);
Log::i("Password Result", $isPasswordValid ? "PASS" : "FAIL");
Log::d('');

// 4. Base64Url 인코딩/디코딩 테스트
$originalData = "This is a test string with special characters: !@#$%^&*()";
$base64Cipher = new CipherGeneric(new Base64UrlEncoder());
$encoded = $base64Cipher->encode($originalData);
$decoded = $base64Cipher->decode($encoded);

Log::d("Base64Url Encoding", $encoded);
Log::d("Base64Url Decoding", $decoded);
Log::i("Base64Url Result", $decoded === $originalData ? "PASS" : "FAIL");
Log::d('');

// 5. 복합 예제: 암호화 -> Base64Url 인코딩 -> 디코딩 -> 복호화
$complexData = "암호화 및 인코딩을 위한 복잡한 데이터";
$complexEncrypted = $aesCipher->encrypt($complexData, $key, $iv);
$complexEncoded = $base64Cipher->encode($complexEncrypted);

$complexDecoded = $base64Cipher->decode($complexEncoded);
$complexDecrypted = $aesCipher->decrypt($complexDecoded, $key, $iv);

Log::d("Complex Encryption + Encoding", $complexEncoded);
Log::d("Complex Decoding + Decryption", $complexDecrypted);
Log::i("Complex Result", $complexDecrypted === $complexData ? "PASS" : "FAIL");
Log::d('');
Log::d('');

// 6. ROT13Encoder 클래스
try {
    CipherGeneric::addProcessor(ROT13Encoder::class);
    $rot13Cipher = new CipherGeneric(new ROT13Encoder($random_text));
    $rot13Encoded = $rot13Cipher->encode();

    $rot13Cipher = new CipherGeneric(new ROT13Encoder($rot13Encoded));
    $rot13Decoded = $rot13Cipher->decode();

    Log::d("Original: " . $random_text);
    Log::d("ROT13 Encoded: " . $rot13Encoded);
    Log::d("ROT13 Decoded: " . $rot13Decoded);
    Log::i("ROT13 Test Result: " . ($random_text === $rot13Decoded ? "PASS" : "FAIL"));
    Log::d('');
} catch (Exception $e) {
    Log::e("Error: " . $e->getMessage());
}

// 7. 허용된 프로세서 목록 출력
Log::d("=== 미리등록된 암호화 관련 클래스 목록 ===");
$allowedProcessors = CipherGeneric::getAllowedProcessors();
foreach ($allowedProcessors as $processor) {
    Log::d($processor);
}


// 8. 나의 암호화 클래스 추가해서 사용하기
class MorseCodeConverter
{
    private $morseAlphabet = [
        'A' => '.-', 'B' => '-...', 'C' => '-.-.', 'D' => '-..', 'E' => '.', 'F' => '..-.',
        'G' => '--.', 'H' => '....', 'I' => '..', 'J' => '.---', 'K' => '-.-', 'L' => '.-..',
        'M' => '--', 'N' => '-.', 'O' => '---', 'P' => '.--.', 'Q' => '--.-', 'R' => '.-.',
        'S' => '...', 'T' => '-', 'U' => '..-', 'V' => '...-', 'W' => '.--', 'X' => '-..-',
        'Y' => '-.--', 'Z' => '--..', '0' => '-----', '1' => '.----', '2' => '..---',
        '3' => '...--', '4' => '....-', '5' => '.....', '6' => '-....', '7' => '--...',
        '8' => '---..', '9' => '----.'
    ];

    public function encode($text)
    {
        $text = strtoupper($text);
        $result = [];
        foreach (str_split($text) as $char) {
            if (isset($this->morseAlphabet[$char])) {
                $result[] = $this->morseAlphabet[$char];
            } elseif ($char === ' ') {
                $result[] = '/';
            }
        }
        return implode(' ', $result);
    }

    public function decode($morse)
    {
        $morse = explode(' ', $morse);
        $result = '';
        foreach ($morse as $code) {
            if ($code === '/') {
                $result .= ' ';
            } else {
                $char = array_search($code, $this->morseAlphabet);
                if ($char !== false) {
                    $result .= $char;
                }
            }
        }
        return $result;
    }
}

$text = "Hello, World!";
CipherGeneric::addProcessor(MorseCodeConverter::class);
$morseConverter = new CipherGeneric(new MorseCodeConverter());
$morseEncode = $morseConverter->encode($text);
$morseDecode = $morseConverter->decode($morseEncode);
Log::d($morseEncode );
Log::d($morseDecode );
?>