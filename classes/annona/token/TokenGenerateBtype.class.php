<?php
namespace Flex\Annona\Token;

use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Token\TokenAbstract;

class TokenGenerateBtype extends TokenAbstract
{
    private string $value = '';

    # 랜덤 : AE68A9MPVZ
    public function __construct(string|null $generate_string, int $length=50){
        $this->value = $generate_string ?? parent::generateString($length);
    }

    # @abstract 해시키 : _md5_base64
    # 5jF4rq3N9V3RLHEBW2RKg
    public function generateHashKey() : TokenGenerateBtype
    {
        $temp_usertoken = (new Encrypt($this->value))->_md5_base64();
        $this->value = parent::cleanEtcWords($temp_usertoken);
    return $this;
    }

    # @abstract 토큰생성 : SHA512
    # 6e262cc52963a523985f368d1f141e6df34125f1dc03fe28fc9abae8db1f185c3b3f0a81f2e271853f9be4c21a0f35c0cdbb5c9d8486168e14dd60a4337df88f
    public function generateToken(string $hash='') : TokenGenerateBtype {
        $this->value = (new Encrypt($this->value))->_hash('sha512');
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