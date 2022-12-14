<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

/**
 * define('_DB_HOST_','127.0.0.1');
 * define('_DB_HOST_','test2');
 * define('_DB_PASSWD_','d1004');
 * define('_DB_NAME_','test_db2');
 * define('_DB_PORT_',33060);
 */
# db
// $db = new \Flex\Db\DbMySqli();
$db = new \Flex\Db\DbMySqli(_DB_HOST_, _DB_HOST_, _DB_PASSWD_, _DB_NAME_, _DB_PORT_);
?>
