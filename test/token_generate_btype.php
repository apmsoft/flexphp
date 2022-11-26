<?php
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# class
# 지정문자로 생성 : 토큰 비교 가능
# $tokenGenerateBtype = new \Flex\Token\TokenGenerateBtype('AE68A9MPVZ');

# 랜덤 문자 생성 : 토큰 비교 불가
$tokenGenerateBtype = new \Flex\Token\TokenGenerateBtype(null,10);

# generateHashKey
$hashKey = $tokenGenerateBtype->generateHashKey($tokenGenerateBtype->generate_string);
echo $hashKey;
echo PHP_EOL;

# 긴토큰 생성
$btoken = $tokenGenerateBtype->generateToken($hashKey);
echo $btoken;
echo PHP_EOL;
?>