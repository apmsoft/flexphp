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

// 예제 사용
// CacheMem 객체 생성
try{
    $cacheMem = new \Flex\Annona\Cache\CacheMem();

    Log::d('CacheMem Version',CacheMem::__version);
    Log::d('Memcache Version',$cacheMem->getVersion());
    // Log::d('Status', $cacheMem->getStats());
    Log::d('서버 상태정보', $cacheMem->_serverStatus() ? '정상' : '서버 에러');

    $data = ["a","b","c"];
    $cache_data = $cacheMem('mc')->_get() ?? $cacheMem->_set($data, 60)->_get();
    Log::d($cache_data);
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>