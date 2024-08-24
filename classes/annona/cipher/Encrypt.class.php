<?php
namespace Flex\Annona\Cipher;

use \Exception;

class Encrypt
{
    public const __version = '2.1';

    private $encryptor;

    /**
     * Encrypt 클래스 생성자
     */
    public function __construct($encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * 암호화, 해시, 또는 Base64Url 인코딩 작업을 수행
     *
     * @param mixed ...$args 추가 인자
     * @return mixed
     * @throws Exception
     */
    public function process(...$args)
    {
        if ($this->encryptor instanceof AES256Hash) {
            return $this->encryptor->encrypt(...$args);
        } elseif ($this->encryptor instanceof HashEncoder) {
            return $this->encryptor->hash();
        } elseif ($this->encryptor instanceof PasswordHash) {
            return $this->encryptor->hash($args[0]);
        } elseif ($this->encryptor instanceof Base64UrlEncoder) {
            return $this->encryptor->encode($args[0]);
        } else {
            throw new Exception("Unsupported encryptor type");
        }
    }

    /**
     * AES256 암호화를 수행
     *
     * @param string $plaintext 암호화할 평문
     * @param string $key 암호화 키
     * @param string $iv 초기화 벡터
     * @return string 암호화된 문자열
     * @throws Exception
     */
    private function aesEncrypt(string $plaintext, string $key, string $iv): string
    {
        if (!$this->encryptor instanceof AES256Hash) {
            throw new Exception("AES256Hash is required for this operation");
        }
        return $this->encryptor->encrypt($plaintext, $key, $iv);
    }

    /**
     * 해시를 생성
     *
     * @param string $data 해시할 데이터
     * @param string $algorithm 해시 알고리즘 (선택적)
     * @return string 해시된 문자열
     * @throws Exception
     */
    private function hash(string $data, string $algorithm = 'sha256'): string
    {
        if (!$this->encryptor instanceof HashEncoder) {
            throw new Exception("HashEncoder is required for this operation");
        }
        return $this->encryptor->hash($algorithm);
    }

    /**
     * 비밀번호를 해시
     *
     * @param string $password 해시할 비밀번호
     * @return string 해시된 비밀번호
     * @throws Exception
     */
    private function hashPassword(string $password): string
    {
        if (!$this->encryptor instanceof PasswordHash) {
            throw new Exception("PasswordHash is required for this operation");
        }
        return $this->encryptor->hash($password);
    }

    /**
     * Base64Url 인코딩 수행
     *
     * @param string $data 인코딩할 데이터
     * @return string Base64Url 인코딩된 문자열
     * @throws Exception
     */
    private function base64UrlEncode(string $data): string
    {
        if (!$this->encryptor instanceof Base64UrlEncoder) {
            throw new Exception("Base64UrlEncoder is required for this operation");
        }
        return $this->encryptor->encode($data);
    }

    /**
     * 내부 encryptor 객체의 메서드를 동적으로 호출
     *
     * @param string $name 호출할 메서드 이름
     * @param array $arguments 메서드에 전달할 인자들
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->encryptor, $name)) {
            return call_user_func_array([$this->encryptor, $name], $arguments);
        }
        throw new Exception("Method $name does not exist in " . get_class($this->encryptor));
    }
}
?>