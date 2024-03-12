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
    $cacheMem = new \Flex\Annona\Cache\CacheMem('mc');

    $data = ["a","b","c"];
    $cache_data = $cacheMem->getCache() ?? $cacheMem->setCache($data, 60)->getCache();
    Log::d($cache_data);
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>