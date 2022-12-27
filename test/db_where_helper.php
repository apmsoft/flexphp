<?php
use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

# config
$path = dirname(__DIR__);
require $path.'/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);
Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# db where 구문 만들기
$whereHelper = new \Flex\Annona\Db\WhereHelper();

# 한줄 코딩
$where = $whereHelper
->begin('AND')
    ->set('name','IN','홍길동,유관순')
    ->set('price','>','0')
    ->set('signdate','is not','NULL')
    ->end()
->begin('OR')
    ->set('price','IN',[1,2,3,4,5,6])
    ->set('price_month','>=',7)
    ->end()
->begin('OR')
    ->set('title','LIKE',['이순신','대통령'])
    ->end()
->fetch();
// ->where;
Log::d( $where );


?>