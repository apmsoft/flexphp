<?php
namespace SMS\Cipher;

use \ErrorException;

# 문자 암호화하기
class CipherEncrypt
{
	private $encrypt_str = '';

	public function __construct($str){
		$this->encrypt_str = $str;
	}

	#@ return String
	# md5
	# enMd5cbf930bbece24547baec219c9089f2eb
	public function _md5()
	{
		$result ='';
		try{
			$result = md5($this->encrypt_str);
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}

	return $result;
	}

	#@ return String
	# md5+base64_encdoe
	# y/kwu+ziRUe67CGckIny6w
	public function _md5_base64(){
		$result ='';
		try{
			$result = preg_replace('/=+$/','',base64_encode(pack('H*',md5($this->encrypt_str))));
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}
	return $result;
	}

	#@ return String
	# md5+utf8_encode
	# cbf930bbece24547baec219c9089f2eb
	public function _md5_utf8encode(){
		$result ='';
		try{
			$result = md5(utf8_encode($this->encrypt_str));
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}
	return $result;
	}

	#@ return String
	# sha512+base64_encode
	# ZDE4OTkyNjE1ZjRlMjgyZmZlMDNjODQxNWQ2ZTZiZDhjN2JkZWRjNDg5MWE5NWU1NDA0Yjk4OTk0MjdmZTc0MmE5ZjU2ZWNhZmQwOWFlNTBlZjVhODNiNTU2NDBiNjcwNzlhZDBkYzE3NWFkMDA3OTU5YjU1YWI2OWJkMzBjMzg=
	public function _hash_base64($hash='sha512'){
		$result ='';
		try{
			$result = base64_encode(hash($hash, $this->encrypt_str));
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}
	return $result;
	}

	#@ return String
	# 디코드 가능한 인코딩
	public function _base64_urlencode(){
		$result = '';
		try{
			$result = urlencode(base64_encode($this->encrypt_str));
		}catch(Exception $e){
			throw new ErrorException($e->getMessage(),__LINE__);
		}
	return $result;
	}
}
?>
