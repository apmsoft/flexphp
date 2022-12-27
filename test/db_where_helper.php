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

# db where 구문 만들기
$whereHelper = new \Flex\Annona\Db\WhereHelper();

# 한줄 코딩
$where = $whereHelper
->begin('AND')->set('name','IN','홍길동,유관순')->set('price','>','0')->set('signdate','is not','NULL')->end()
->begin('OR')->set('price','IN',[1,2,3,4,5,6])->set('price_month','>=',7)->end()
->begin('OR')->set('title','LIKE',['이순신','대통령'])->end()
->fetch();
// ->where;
Log::d( $where );


Log::d("==============================");

# 멀리 라인 코딩
$whereHelper->begin('AND');
    $whereHelper->set('name', 'IN' , '홍길동,유관순');
    $whereHelper->set('age', '>=' , 10);
    $whereHelper->set('job', 'IN' , ['공무원','프로그래머','경영인','디자이너']);
    $whereHelper->set("JSON_UNQUOTE(detail_info->'$.deli.dome.type')", 'LIKE-R', "d" ,true,false);
    $whereHelper->set("signdate", 'is not', 'NULL');
    $whereHelper->set('price', '>', '0');
$whereHelper->end();

$whereHelper->begin('OR');
    $whereHelper->set('price', 'IN' , [1,2,3,4,5,6]);
    $whereHelper->set('price_month', '>=' , 7);
$whereHelper->end();

$whereHelper->begin('OR');
    $whereHelper->set('title', 'LIKE' , ['이순신','대통령']);
$whereHelper->end();

# string, array 출력하기
Log::d('groups data',$whereHelper->fetch());

# where 문 출력
// Log::d( $whereHelper->where);


?>