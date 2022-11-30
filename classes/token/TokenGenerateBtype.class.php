<?php
namespace Flex\Token;

use Flex\R\R;
use Flex\Cipher\CipherEncrypt;
use Flex\Log\Log;

/** ex)
 *$tokenGenerateBtype = new \Flex\Token\TokenGenerateBtype(null,10);
 *$hsahkey = $tokenGenerateBtype->generateHashKey($tokenGenerateBtype->generate_string);
 *$tokenGenerateBtype->generateToken($hsahkey);
 */
class TokenGenerateBtype extends TokenSwitch
{
    const TAG = 'TokenGenerateBtype::';
    public string $generate_string = '';

    # 랜덤 : AE68A9MPVZ
    public function __construct(string|null $generate_string, int $length=50){
        $this->generate_string = $generate_string ?? parent::generateString($length);
        #Log::d($this->generate_string);
    }

    # @abstract 해시키 : _md5_base64
    # 5jF4rq3N9V3RLHEBW2RKg
    public function generateHashKey(string $generate_string) : string
    {
        $cipherEncrypt  = new CipherEncrypt($generate_string);
        $temp_usertoken = $cipherEncrypt->_md5_base64();
        return parent::cleanEtcWords($temp_usertoken);
    }

    # @abstract 토큰생성 : SHA512
    # 6e262cc52963a523985f368d1f141e6df34125f1dc03fe28fc9abae8db1f185c3b3f0a81f2e271853f9be4c21a0f35c0cdbb5c9d8486168e14dd60a4337df88f
    public function generateToken(string $hash_token) : string {
        $cipherEncrypt = new CipherEncrypt($hash_token);
        return $cipherEncrypt->_hash('sha512');
    }
}
?>