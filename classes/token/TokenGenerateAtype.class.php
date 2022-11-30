<?php
namespace Flex\Token;

use Flex\R\R;
use Flex\Cipher\CipherEncrypt;
use Flex\Cipher\CipherDecrypt;

/** ex)
 * $generate_string = implode('',[$module_id,$secret_key]);
 * $tokenGenerateAtype = new \Flex\Token\TokenGenerateAtype($generate_string);
 * $hsahkey = $tokenGenerateAtype->generateHashKey();
 * $hash_token = implode(__TOKEN_CHARACTER__,[$module_id,$hashkey]);
 * $tokenGenerateAtype->generateToken($hash_token);
 */
class TokenGenerateAtype extends TokenSwitch
{
    const TAG = 'TokenGenerateAtype::';
    public string $generate_string = '';

    public function __construct(string|null $generate_string, int $length=50){
        $this->generate_string = $generate_string ?? parent::generateString($length);
    }

    # @abstract 해시키 : SHA512 | SHA256
    public function generateHashKey(string $generate_string, string $encrypt_type ='sha512') : string
    {
        $cipherEncrypt = new CipherEncrypt($generate_string);
        $secret_key = match ($encrypt_type) {
            'sha256','sha512' => $cipherEncrypt->_hash($encrypt_type),
            'md5' => $cipherEncrypt->_md5(),
        };
        return $secret_key;
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function generateToken(string $hash_token) : string {
        $cipherEncrypt = new CipherEncrypt($hash_token);
        return $cipherEncrypt->_base64_urlencode();
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function decodeToken(string $token) : string {
        $cipherDecrypt = new CipherDecrypt($token);
        return $cipherDecrypt->_base64_urldecode();
    }
}
?>