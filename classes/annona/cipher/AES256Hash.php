<?php
namespace Flex\Annona\Cipher;

use Exception;

class AES256Hash
{
    public const __version = '1.0';

    private string $encrypt_method = 'AES-256-CBC';

    /**
     * AES-256 암호화를 수행
     *
     * @param string $plaintext 암호화할 평문
     * @param string $secret_key 비밀 키 random_bytes(32) | hex2bin($hex_string) 
     * @param string $secret_iv 초기화 벡터 (IV) random_bytes(16) | bin2hex
     * @return string 암호화된 문자열 (base64 인코딩됨)
     * @throws Exception 암호화 실패 시
     */
    public function encrypt(string $plaintext, string $secret_key, string $secret_iv): string
    {
        $key = $this->prepareKey($secret_key);
        $iv = $this->prepareIV($secret_iv);

        $encrypted = openssl_encrypt($plaintext, $this->encrypt_method, $key, 0, $iv);

        if ($encrypted === false) {
            return '';
        }

        return $encrypted;
    }

    /**
     * AES-256 복호화를 수행
     *
     * @param string $ciphertext 복호화할 암호문 (base64 인코딩된 상태)
     * @param string $secret_key 비밀 키
     * @param string $secret_iv 초기화 벡터 (IV)
     * @return string 복호화된 평문
     * @throws Exception 복호화 실패 시
     */
    public function decrypt(string $ciphertext, string $secret_key, string $secret_iv): string
    {
        $key = $this->prepareKey($secret_key);
        $iv = $this->prepareIV($secret_iv);

        $decrypted = openssl_decrypt($ciphertext, $this->encrypt_method, $key, 0, $iv);

        if ($decrypted === false) {
            return '';
        }

        return $decrypted;
    }

    /**
     * 비밀 키를 준비 (32바이트로 조정)
     *
     * @param string $secret_key 원본 비밀 키
     * @return string 32바이트로 조정된 키
     */
    private function prepareKey(string $secret_key): string
    {
        if (strlen($secret_key) === 32) {
            return $secret_key;
        }
        if (strlen($secret_key) > 32) {
            return substr($secret_key, 0, 32);
        }
        return str_pad($secret_key, 32, "\0", STR_PAD_RIGHT);
    }

    /**
     * 초기화 벡터(IV)를 준비 (16바이트로 조정)
     *
     * @param string $secret_iv 원본 IV
     * @return string 16바이트로 조정된 IV
     */
    private function prepareIV(string $secret_iv): string
    {
        if (strlen($secret_iv) === 16) {
            return $secret_iv;
        }
        if (strlen($secret_iv) > 16) {
            return substr($secret_iv, 0, 16);
        }
        return str_pad($secret_iv, 16, "\0", STR_PAD_RIGHT);
    }
}
?>