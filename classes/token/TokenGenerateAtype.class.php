<?php
namespace Flex\Token;

use Flex\Cipher\CipherEncrypt;
use Flex\Cipher\CipherDecrypt;
use Flex\Token\TokenAbstract;

class TokenGenerateAtype extends TokenAbstract
{
    const TAG = 'TokenGenerateAtype::';
    public string $value = '';

    public function __construct(string|null $generate_string, int $length=50){
        $this->value = $generate_string ?? parent::generateString($length);
    }

    # @abstract 해시키 : SHA512 | SHA256
    public function generateHashKey(string $encrypt_type ='sha512') : TokenGenerateAtype
    {
        $this->value = match ($encrypt_type) {
            'sha256','sha512' => (new CipherEncrypt($this->value))->_hash($encrypt_type),
            'md5' => (new CipherEncrypt($this->value))->_md5()
        };
    return $this;
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function generateToken(string $hash) : TokenGenerateAtype {
        $this->value = (new CipherEncrypt(sprintf("%s%s",$hash,$this->value)))->_base64_urlencode();
    return $this;
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function decodeToken(string $token) : TokenGenerateAtype {
        $this->value = (new CipherDecrypt($token))->_base64_urldecode();
    return $this;
    }

    public function __get(string $propertyName){
        $result = '';
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}
?>