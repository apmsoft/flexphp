<?php
namespace Flex\Annona\Cache;

use \Memcached;

class CacheMem extends Memcached
{
    public const __version = '0.1';
    public function __construct(string $host='localhost', int $port=11211) {
        parent::__construct();
        parent::addServer($host, $port);
    }

    /**
     * key : 캐시키
     * data : 캐시데이터
     * expiration : 캐시 유지 시간
     */
    public function set(string $key, mixed $data, int $expiration = 0) :bool {
        return parent::set($key, $data, $expiration);
    }

    # 캐시 가져오기
    public function get(string $key) : mixed {
        return parent::get($key);
    }

    # 캐시 삭제
    public function delete(string $key) : bool{
        return parent::delete($key);
    }

    # 캐시 비우기
    public function clear() : bool{
        return parent::flush();
    }

    // 매직 메서드로 호출되는 __call() 함수 추가
    /**
     * has : 캐시키가 있는지 체크
     * then : 콜백
     */
    public function __call($method, $args) : mixed {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }
        // has() 메서드와 then() 메서드 호출 확인
        if ($method === 'has' && isset($args[0])) {
            return $this->get($args[0]) !== false ? true : null;
        } else if ($method === 'then' && isset($args[0]) && is_callable($args[0])) {
            return $args[0]($this->get($args[1]));
        }
    }
}
?>