<?php
namespace Flex\Token;

use Flex\R\R;
use Flex\Cipher\CipherEncrypt;

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

    # @abstract 해시키 : SHA512
    public function generateHashKey(string $generate_string) : string
    {
        $cipherEncrypt = new CipherEncrypt($generate_string);
        return $cipherEncrypt->_hash('sha512');
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function generateToken(string $hash_token) : string {
        $cipherEncrypt = new CipherEncrypt($hash_token);
        return $cipherEncrypt->_base64_urlencode();
    }
}
?>