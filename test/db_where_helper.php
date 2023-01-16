<?php
use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

use Flex\Annona\Db\WhereHelper;

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
// $whereHelper = new \Flex\Annona\Db\WhereHelper();
// $where = $whereHelper
// ->begin('AND')
//     ->case('name','IN','홍길동,유관순')
//     ->case('price','>','0')
//     ->case('signdate','is not','NULL')->end()
// ->begin('OR')
//     ->case('price','IN',[1,2,3,4,5,6])
//     ->case('price_month','>=',7)->end()
// ->begin('OR')
//     ->case('title','LIKE',['이순신','대통령'])->end()
// // ->fetch();
// ->where;
// Log::d( $where );

// # direct
// $where2 = (new \Flex\Annona\Db\WhereHelper())
// ->begin('AND')
//     ->case('price','>','0')->end()
// ->begin('OR')
//     ->case('price_month','>=',7)->end()
// ->begin('OR')
//     ->case('title','LIKE',['이순신','대통령'])->end()
// // ->fetch();
// ->where;
// Log::d( $where2 );

$whereHelper = new WhereHelper();
$whereHelper->begin('AND')->case('userid', '=', 'test@ddd.com',true)->end();
Log::d($whereHelper->where);

$whereHelper = new WhereHelper();
$whereHelper->begin('AND')->case('userid', '=', 'a2_.com_23',true)->end();
Log::d($whereHelper->where);
?>