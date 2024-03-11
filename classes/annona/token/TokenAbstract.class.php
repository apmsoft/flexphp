<?php
namespace Flex\Annona\Token;

use Flex\Annona\Random\Random;

abstract class TokenAbstract
{
    public const __version = '1.1';
    protected function cleanEtcWords(string $token){
        return preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i",'',$token);
    }

    # 길이만큼랜덤으로 문자를 조합
    public function generateString(int $length) : string
    {
        return (new Random([]))->array($length);
    }

    # 특정한 문자를 암호화 한다.
    public function generateSecretKey(string $secret_token) : string{
        return md5(utf8_encode($secret_token));
    }

    # 해시키 만들기
    abstract public function generateHashKey() : mixed;

    # 토큰생성
    abstract public function generateToken(string $hash) : mixed;
}
?>