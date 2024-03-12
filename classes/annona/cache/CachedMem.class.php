<?php
namespace Flex\Annona\Cache;

use \Memcached;

class CachedMem extends Memcached
{
    public const __version = '0.1';
    private string $cache_key;

    public function __construct(string $cache_key, string $host='localhost', int $port=11211) 
    {
        parent::__construct();
        parent::addServer($host, $port);
    }

    /**
     * 캐시에 키-값 쌍을 설정
     *
     * @param mixed $data 캐싱될 데이터
     * @param int $expiration 캐시 만료 시간 = 0 - 만료되지 않음, 초 단위 >0
     */
    public function setCache(mixed $data, int $expiration = 0) :CachedMem {
        if (!$this->set($this->cache_key, $data, $expiration)){
            throw new \Exception("Memcache set failed for key: {$this->cache_key}");
        }
        return $this;
    }

    # 캐시 가져오기
    public function getCache(string $key) : mixed {
        $data = $this->get($this->cache_key);
        if ($data === false) {
            $data = null; // 캐시 값이 없는 경우
        }
        return $data;
    }

    # 캐시 삭제
    public function deleteCache(int $time = 0) : void{
        if(!$this->delete($this->cache_key)){
            throw new \Exception("Memcache delete failed for key: {$this->cache_key}");
        }
    }

    # 캐시 비우기
    public function clear() : void{
        if(!$this->flush()){
            throw new \Exception('Memcache clear failed');
        }
    }
}
?>