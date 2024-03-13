<?php
namespace Flex\Annona\Cache;

use \Memcache;

class CacheMem
{
    public const __version = '0.5.1';
    private string $cache_key;

    private Memcache $memcache;

    public function __construct(
        private string $host='localhost',
        private int $port=11211
    ){
        $this->memcache = new Memcache;
        if(!$this->memcache->connect($this->host, $this->port)){
            throw new \Exception("Memcache connect fail...");
        }
    }

    public function __invoke(string $cache_key): CacheMem
    {
        $this->cache_key = $cache_key;

        return $this;
    }

    public function __call(string $method,array $params) : mixed
    {
        if(method_exists($this->memcache,$method))
        {
            $is_flag = match($method){
                'set','get','delete','flush','close' => false,
                default => true
            };
            return ($is_flag) ?
                call_user_func_array(array($this->memcache,$method),$params) :
                    throw new \Exception("Memcache not exists method: {$method}");
        } else throw new \Exception("Memcache not exists method: {$method}");

    }

    # 서버 상태 체크 : 서버가 실패하면 0, 그렇지 않으면 0이 아님
    public function _serverStatus() : mixed
    {
        if(empty($this->memcache->getServerStatus($this->host, $this->port))){
            return null;
        }
    return true;
    }

    /**
     * 캐시에 키-값 쌍을 설정
     *
     * @param mixed $data 캐싱될 데이터
     * @param int $expiration 캐시 만료 시간 = 0 - 만료되지 않음, 초 단위 >0
     */
    public function _set(mixed $data, int $expiration = 0): CacheMem
    {
        if (!$this->memcache->set($this->cache_key, $data, 0, $expiration)) {
            throw new \Exception("Memcache set failed for key: {$this->cache_key}");
        }
        return $this;
    }

    # 캐시에서 값을 가져오기
    public function _get(): mixed
    {
        $data = $this->memcache->get($this->cache_key);
        if ($data === false) {
            $data = null; // 캐시 값이 없는 경우
        }

        return $data;
    }

    # 캐시에서 키를 삭제
    public function _delete(int $time = 0): void
    {
        if(!$this->memcache->delete($this->cache_key, $time)){
            throw new \Exception("Memcache delete failed for key: {$this->cache_key}");
        }
    }

    # 캐시 비우기
    public function _clear(): void
    {
        if(!$this->memcache->flush()){
            throw new \Exception('Memcache clear failed');
        }
    }

    # 캐시 접속 종료
    public function _close() : void
    {
        if(!$this->memcache->close()){
            throw new \Exception('Memcache close failed');
        }
    }

    # 자동소멸
    public function __destruct(){
        $this->_close();
    }
}

?>