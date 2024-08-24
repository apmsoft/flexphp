<?php
namespace Flex\Annona\Cipher;

use \Exception;

# 문자 복호화하기
class Decrypt
{
	public const __version = '1.1.1';
	private $decrypt_str = '';

	public function __construct(string $str){
		$this->decrypt_str = $str;
	}

	#@ return String
	public function _base64_urldecode() : string{
		$result = base64_decode(urldecode($this->decrypt_str)) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}

	# AES 256
	public function _aes256_decrypt(string $secret_key, string $secret_iv, string $encrypt_method='AES-256-CBC') : string 
	{
		$hash_key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256',$secret_iv), 0, 16);

		$result = openssl_decrypt($this->decrypt_str, $encrypt_method, $hash_key, 0, $iv);

	return $result;
	}
}
?>
