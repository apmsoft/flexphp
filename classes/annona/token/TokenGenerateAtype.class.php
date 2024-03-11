<?php
namespace Flex\Annona\Token;

use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Cipher\Decrypt;
use Flex\Annona\Token\TokenAbstract;

class TokenGenerateAtype extends TokenAbstract
{
    public const __version = '1.1';
    public string $value = '';

    public function __construct(string|null $generate_string, int $length=50){
        $this->value = $generate_string ?? parent::generateString($length);
    }

    # @abstract 해시키 : SHA512 | SHA256
    public function generateHashKey(string $encrypt_type ='sha512') : TokenGenerateAtype
    {
        $this->value = match ($encrypt_type) {
            'sha256','sha512' => (new Encrypt($this->value))->_hash($encrypt_type),
            'md5' => (new Encrypt($this->value))->_md5()
        };
    return $this;
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function generateToken(string $hash) : TokenGenerateAtype {
        $this->value = (new Encrypt(sprintf("%s%s",$hash,$this->value)))->_base64_urlencode();
    return $this;
    }

    # @abstract 토큰생성 : _base64_urlencode
    public function decodeToken(string $token) : TokenGenerateAtype {
        $this->value = (new Decrypt($token))->_base64_urldecode();
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