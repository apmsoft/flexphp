<?php

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Schema\TablesMap;
use Flex\Annona\Array\ArrayHelper;
use Flex\Annona\Model;
use Flex\Components\Adapter\BaseAdapter;
use Flex\Annona\Uuid\UuidGenerator;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

$baseAdapter = new BaseAdapter();


?>