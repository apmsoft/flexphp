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
    public function encode($str) {
        return str_rot13($str);
    }
    public function decode($str) {
        return str_rot13($str);
    }
}
?>