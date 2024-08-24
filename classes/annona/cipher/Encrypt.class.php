<?php
namespace Flex\Annona\Cipher;

use \Exception;

# 문자 암호화하기
# _hash함수추가
class Encrypt
{
	public const __version = '1.1.1';
	private $encrypt_str = '';

	public function __construct(string $str){
		$this->encrypt_str = $str;
	}

	# md5
	# enMd5cbf930bbece24547baec219c9089f2eb
	public function _md5() : string{
		$result = md5($this->encrypt_str) ?? throw new Exception($e->getMessage(),__LINE__);

	return $result;
	}

	# md5+base64_encdoe
	# y/kwu+ziRUe67CGckIny6w
	public function _md5_base64() : string{
		$result = preg_replace('/=+$/','',base64_encode(pack('H*',md5($this->encrypt_str)))) ?? throw new Exception($e->getMessage(),__LINE__);

	return $result;
	}

	# md5+utf8_encode
	# cbf930bbece24547baec219c9089f2eb
	public function _md5_utf8encode() : string{
		$result = md5(utf8_encode($this->encrypt_str)) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}

	# sha512 || sha256
	public function _hash(string $hash='sha256') : string{
		$result = hash($hash, $this->encrypt_str) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}

	# sha512+base64_encode || sha256+base64_encode
	# ZDE4OTkyNjE1ZjRlMjgyZmZlMDNjODQxNWQ2ZTZiZDhjN2JkZWRjNDg5MWE5NWU1NDA0Yjk4OTk0MjdmZTc0MmE5ZjU2ZWNhZmQwOWFlNTBlZjVhODNiNTU2NDBiNjcwNzlhZDBkYzE3NWFkMDA3OTU5YjU1YWI2OWJkMzBjMzg=
	public function _hash_base64(string $hash='sha256') : string{
		$result = base64_encode(hash($hash, $this->encrypt_str)) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}

	# 디코드 가능한 인코딩
	public function _base64_urlencode() : string{
		$result = urlencode(base64_encode($this->encrypt_str)) ?? throw new Exception($e->getMessage(),__LINE__);
	return $result;
	}

	# AES 256
	public function _aes256_encrypt(string $secret_key, string $secret_iv, string $encrypt_method='AES-256-CBC') : string 
	{
		$hash_key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256',$secret_iv), 0, 16);

		$result = openssl_encrypt($this->encrypt_str, $encrypt_method, $hash_key, 0, $iv);
	return $result;
	}
}
?>
