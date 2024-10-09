<?php
namespace Flex\Annona\Cipher;

/**
 * 알파벳을 13글자씩 밀어서 치환
 * 대소문자를 구분
 * ex) A->N, B->O, Z->M,
 * 알파벳이 아닌 문자(숫자, 특수문자, 공백 등)는 변경하지 않고 그대로 둡
 */
class ROT13Encoder 
{
    public const __version = '1.0';
	private $encrypt_str = '';

	public function __construct(string $str){
		$this->encrypt_str = $str;
	}
    public function encode() {
        return str_rot13($this->encrypt_str);
    }
    public function decode() {
        return str_rot13($this->encrypt_str);
    }
}
?>