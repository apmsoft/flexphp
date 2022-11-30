<?php
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# class
$module_id = 'comflexphp';
$secret_key = '';
define('__TOKEN_CHARACTER__','.');

echo 'module_id : '.$module_id.PHP_EOL;

# 시크릿키 생성
$tokenGenerateAtype = new \Flex\Token\TokenGenerateAtype(null|5);

# sha256 | sha512 | md5
$secret_key = $tokenGenerateAtype->generateHashKey($tokenGenerateAtype->generate_string,'sha256');
echo 'secret_key : '.$secret_key.PHP_EOL;

# 토큰만들기
$access_token = $tokenGenerateAtype->generateToken( implode(__TOKEN_CHARACTER__, [$module_id, $secret_key]) );
echo 'access_token : '.$access_token.PHP_EOL;

# 토큰 디코딩
$decode_access_token = $tokenGenerateAtype->decodeToken($access_token);
echo 'decoding access_token : '.$decode_access_token.PHP_EOL;
?>