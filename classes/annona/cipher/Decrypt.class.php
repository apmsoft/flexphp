<?php
namespace Flex\Annona\Cipher;

use \Exception;

class Decrypt
{
    public const __version = '1.0';

    private $decryptor;

    /**
     * Decrypt 클래스 생성자
     */
    public function __construct($decryptor)
    {
        $this->decryptor = $decryptor;
    }

    /**
     * 복호화, 해시 검증, 또는 Base64Url 디코딩 작업을 수행
     *
     * @param mixed ...$args 추가 인자
     * @return mixed
     * @throws Exception
     */
    public function process(...$args)
    {
        if ($this->decryptor instanceof AES256Hash) {
            return $this->decryptor->decrypt(...$args);
        } elseif ($this->decryptor instanceof HashEncoder) {
            $computedHash = $this->decryptor->hash();
            return hash_equals($args[0], $computedHash);
        } elseif ($this->decryptor instanceof PasswordHash) {
            return $this->decryptor->verify($args[0], $args[1]);
        } elseif ($this->decryptor instanceof Base64UrlEncoder) {
            return $this->decryptor->decode($args[0]);
        } else {
            throw new Exception("Unsupported decryptor type");
        }
    }

    /**
     * AES256 복호화를 수행
     *
     * @param string $ciphertext 복호화할 암호문
     * @param string $key 암호화 키
     * @param string $iv 초기화 벡터
     * @return string 복호화된 평문
     * @throws Exception
     */
    private function aesDecrypt(string $ciphertext, string $key, string $iv): string
    {
        if (!$this->decryptor instanceof AES256Hash) {
            throw new Exception("AES256Hash is required for this operation");
        }
        return $this->decryptor->decrypt($ciphertext, $key, $iv);
    }

    /**
     * 해시를 검증
     *
     * @param string $data 검증할 데이터
     * @param string $hash 비교할 해시
     * @param string $algorithm 해시 알고리즘 (선택적)
     * @return bool 해시 일치 여부
     * @throws Exception
     */
    private function verifyHash(string $data, string $hash, string $algorithm = 'sha256'): bool
    {
        if (!$this->decryptor instanceof HashEncoder) {
            throw new Exception("HashEncoder is required for this operation");
        }
        $computedHash = $this->decryptor->hash($data, $algorithm);
        return hash_equals($hash, $computedHash);
    }

    /**
     * 비밀번호를 검증
     *
     * @param string $password 검증할 비밀번호
     * @param string $hash 저장된 해시
     * @return bool 비밀번호 일치 여부
     * @throws Exception
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        if (!$this->decryptor instanceof PasswordHash) {
            throw new Exception("PasswordHash is required for this operation");
        }
        return $this->decryptor->verify($password, $hash);
    }

    /**
     * Base64Url 디코딩 수행
     *
     * @param string $data 디코딩할 Base64Url 문자열
     * @return string 디코딩된 원본 데이터
     * @throws Exception
     */
    private function base64UrlDecode(string $data): string
    {
        if (!$this->decryptor instanceof Base64UrlEncoder) {
            throw new Exception("Base64UrlEncoder is required for this operation");
        }
        return $this->decryptor->decode($data);
    }

    /**
     * 내부 decryptor 객체의 메서드를 동적으로 호출
     *
     * @param string $name 호출할 메서드 이름
     * @param array $arguments 메서드에 전달할 인자들
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->decryptor, $name)) {
            return call_user_func_array([$this->decryptor, $name], $arguments);
        }
        throw new Exception("Method $name does not exist in " . get_class($this->decryptor));
    }
}
?>