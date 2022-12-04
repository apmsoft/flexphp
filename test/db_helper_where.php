<?php
use Flex\App\App;
use Flex\R\R;
use Flex\Log\Log;

# config
$path = dirname(__DIR__);
require $path.'/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# db where 구문 만들기
$dbHelperWhere = new \Flex\Db\DbHelperWhere();
$dbHelperWhere->beginWhereGroup('groupa', 'AND');
    $dbHelperWhere->setBuildWhere('name', 'IN' , '홍길동,유관순', true);
    $dbHelperWhere->setBuildWhere('age', '>=' , 10, true);
    $dbHelperWhere->setBuildWhere('job', 'IN' , ['공무원','프로그래머','경영인','디자이너'], true);
    $dbHelperWhere->setBuildWhere("JSON_UNQUOTE(detail_info->'$.deli.dome.type')", 'LIKE-R', "d" ,true,false);
    $dbHelperWhere->setBuildWhere("signdate", 'is not', 'NULL' ,true,false);
    $dbHelperWhere->setBuildWhere('price', '>', '0',true);
$dbHelperWhere->endWhereGroup();

$dbHelperWhere->beginWhereGroup('groupb', 'OR');
    $dbHelperWhere->setBuildWhere('price', 'IN' , [1,2,3,4,5,6], true);
    $dbHelperWhere->setBuildWhere('price_month', '>=' , 7, true);
$dbHelperWhere->endWhereGroup();

$dbHelperWhere->beginWhereGroup('groupc', 'OR');
    $dbHelperWhere->setBuildWhere('title', 'LIKE' , ['이순신','대통령'], false);
$dbHelperWhere->endWhereGroup();

# string, array 출력하기
Log::d('groups data',$dbHelperWhere->fetch());

# where 문 출력
Log::d( $dbHelperWhere->where);
?>