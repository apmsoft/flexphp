<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 1.0
----------------------------------------------------------*/
namespace Fus3\Cipher;

use \ErrorException;

# 문자 복호화하기
class CipherDecrypt
{
	private $decrypt_str = '';

	public function __construct($str){
		$this->decrypt_str = $str;
	}

	#@ return String
	public function _base64_urldecode(){
		$result = '';
		try{
			$result = base64_decode(urldecode($this->decrypt_str));
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}
	return $result;
	}
}
?>
