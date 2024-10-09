<?php
namespace Flex\Annona\Cipher;


class PasswordHash
{
    public const __version = '1.0';
    private array $options;

    /**
     * PasswordHasher 생성자
     *
     * @param int $memory_cost 메모리 사용량 (KiB, 기본값: 65536)
     * @param int $time_cost 반복 횟수 (기본값: 4)
     * @param int $threads 사용할 스레드 수 (기본값: 1)
     */
    public function __construct(int $memory_cost = 65536, int $time_cost = 4, int $threads = 1)
    {
        $this->options = [
            'memory_cost' => $memory_cost,
            'time_cost' => $time_cost,
            'threads' => $threads,
        ];
    }

    /**
     * 비밀번호를 해시.
     *
     * @param string $password 해시할 비밀번호
     * @return string 해시된 비밀번호
     */
    public function hash(string $password): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID, $this->options);
        } else {
            // Argon2id를 사용할 수 없는 경우 bcrypt로
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    /**
     * 일치여부 비교
     *
     * @param string $password 확인할 비밀번호
     * @param string $hash 비교할 해시
     * @return bool 비밀번호가 일치하면 true, 그렇지 않으면 false
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 해시가 재해싱이 필요한지 확인
     *
     * @param string $hash 확인할 해시
     * @return bool 재해싱이 필요하면 true, 그렇지 않으면 false
     */
    public function needsRehash(string $hash): bool
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_needs_rehash($hash, PASSWORD_ARGON2ID, $this->options);
        } else {
            return password_needs_rehash($hash, PASSWORD_DEFAULT);
        }
    }

    /**
     * 해시를 업그레이드
     *
     * @param string $password 원본 비밀번호
     * @param string $hash 현재 해시
     * @return string|null 새로운 해시 또는 업그레이드가 필요 없으면 null
     */
    public function upgradeHash(string $password, string $hash): ?string
    {
        if ($this->needsRehash($hash)) {
            return $this->hash($password);
        }
        return null;
    }
}