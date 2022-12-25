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
$whereHelper->beginWhereGroup('groupa', 'AND');
    $whereHelper->setBuildWhere('name', 'IN' , '홍길동,유관순', true);
    $whereHelper->setBuildWhere('age', '>=' , 10, true);
    $whereHelper->setBuildWhere('job', 'IN' , ['공무원','프로그래머','경영인','디자이너'], true);
    $whereHelper->setBuildWhere("JSON_UNQUOTE(detail_info->'$.deli.dome.type')", 'LIKE-R', "d" ,true,false);
    $whereHelper->setBuildWhere("signdate", 'is not', 'NULL' ,true,false);
    $whereHelper->setBuildWhere('price', '>', '0',true);
$whereHelper->endWhereGroup();

$whereHelper->beginWhereGroup('groupb', 'OR');
    $whereHelper->setBuildWhere('price', 'IN' , [1,2,3,4,5,6], true);
    $whereHelper->setBuildWhere('price_month', '>=' , 7, true);
$whereHelper->endWhereGroup();

$whereHelper->beginWhereGroup('groupc', 'OR');
    $whereHelper->setBuildWhere('title', 'LIKE' , ['이순신','대통령'], false);
$whereHelper->endWhereGroup();

# string, array 출력하기
Log::d('groups data',$whereHelper->fetch());

# where 문 출력
Log::d( $whereHelper->where);
?>