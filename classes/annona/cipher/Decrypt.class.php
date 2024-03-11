<?php
namespace Flex\Annona\Cipher;

use \Exception;

# 문자 복호화하기
class Decrypt
{
	public const __version = '1.1';
	private $decrypt_str = '';

	public function __construct(string $str){
		$this->decrypt_str = $str;
	}

	#@ return String
	public function _base64_urldecode() : string{
		$result = base64_decode(urldecode($this->decrypt_str)) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}
}
?>
