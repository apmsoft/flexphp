<?php
namespace Flex\Token;

use Flex\R\R;
use Flex\Cipher\CipherEncrypt;
use Flex\Log\Log;

abstract class TokenSwitch
{
    protected function cleanEtcWords(string $token){
        return preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i",'',$token);
    }

    # 길이만큼랜덤으로 문자를 조합
    public function generateString(int $length) : string
    {
        $stringRandom = new \Flex\String\StringRandom(null);
        return $stringRandom->arrayRand($length);
    }

    # 특정한 문자를 암호화 한다.
    public function generateSecretKey(string $secret_token) : string{
        return md5(utf8_encode($secret_token));
    }

    # 해시키 만들기
    abstract public function generateHashKey(string $generate_string) : string;

    # 토큰생성
    abstract public function generateToken(string $token) : string;
}
?>