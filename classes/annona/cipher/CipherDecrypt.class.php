<?php
namespace Flex\Annona\Cipher;

use \ErrorException;

# 문자 복호화하기
class CipherDecrypt
{
	private $decrypt_str = '';

	public function __construct(string $str){
		$this->decrypt_str = $str;
	return $this;
	}

	#@ return String
	public function _base64_urldecode() : string{
		$result = base64_decode(urldecode($this->decrypt_str)) ?? throw new ErrorException($e->getMessage(),__LINE__);
	return $result;
	}
}
?>
