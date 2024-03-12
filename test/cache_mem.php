<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;
use Flex\Annona\Cache\CacheMem;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

// 외부 함수 정의
function getAndCacheFooFromDb($cache, $key) {
    // 예시로 'foo' 값을 직접 반환
    return 'value_from_db';
}


$cache = new CacheMem('localhost', 11211);

// 예제 사용
// CacheMem 객체 생성
$cache = new \Flex\Annona\Cache\CacheMem();

// 캐시할 데이터 설정
$key = 'mc';

// 캐시된 데이터가 있는지 체크하고 있을 경우에는 캐시 데이터를 가져옴
$cache->has($key)?->
    then(function ($result) use ($cache, $key) {
        if($result !=null){
            Log::d( "get Cached Data: ", $result);
            return $result = $cache->get($key);
        }
    })
    ->then(function ($result) use ($cache, $key) {
        if($result ==null){
            // 캐시된 데이터가 없는 경우, 데이터를 가져와서 캐시에 저장
            $result = array('foo' => 'bar', 'hello' => 'world');

            // 데이터를 캐시에 설정 (유효 시간: 60초)
            $cacheTime = 60;
            $cache->set($key, $data, $cacheTime);
            Log::d( "Data not cached.");
        }
        return $result;
    });
?>