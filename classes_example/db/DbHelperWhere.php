<?php
use Flex\App\App;
use Flex\R\R;

# config
$path = dirname(dirname(__DIR__));
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# db where 구문 만들기
$dbHelperWhere = new Flex\Db\DbHelperWhere();
$dbHelperWhere->beginWhereGroup('groupa', 'AND');
$dbHelperWhere->setBuildWhere('name', 'IN' , '홍길동,유관순', true);
$dbHelperWhere->setBuildWhere('age', '>=' , 10, true);
$dbHelperWhere->setBuildWhere('job', 'IN' , ['공무원','프로그래머','경영인','디자이너'], true);
$dbHelperWhere->endWhereGroup();

$dbHelperWhere->beginWhereGroup('groupb', 'OR');
$dbHelperWhere->setBuildWhere('price', 'IN' , [1,2,3,4,5,6], 'OR', true);
$dbHelperWhere->setBuildWhere('price_month', '>=' , 7, 'OR', true);
$dbHelperWhere->endWhereGroup();

$dbHelperWhere->beginWhereGroup('groupc', 'OR');
$dbHelperWhere->setBuildWhere('title', 'LIKE' , ['이순신','대통령'], 'OR', false);
// $dbHelperWhere->setBuildWhere('title', 'LIKE-L' , ['이순신','대통령'], 'OR', true);
// $dbHelperWhere->setBuildWhere('title', 'LIKE-R' , ['이순신','대통령'], 'OR', true);
$dbHelperWhere->endWhereGroup();

out_ln ($dbHelperWhere->where);
?>