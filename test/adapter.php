<?php

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Schema\TablesMap;
use Flex\Annona\Array\ArrayHelper;
use Flex\Annona\Model;
use Flex\Annona\Adapter\BaseAdapter;
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

$model = $baseAdapter->new(Model::class, []);
$model->a = 1;
$model->b = 4;
$model->data = [];
$model->{"data+"} = 1;
$model->{"data+"} = 2;
$model->{"data+"} = 3;

Log::d($model->fetch());

$model2 = $baseAdapter->new(Model::class, ["a2"=>"a2"]);
Log::d($model2->fetch());

$baseAdapter->new(ArrayHelper::class, [["a"=>"a"],["a"=>"b"]]);
Log::d($baseAdapter->ArrayHelper->sorting('a','DESC')->value);

$baseAdapter->new(UuidGenerator::class);
Log::d($baseAdapter->UuidGenerator->v4());


$baseAdapter->new('Flex\Annona\Paging\Relation', 1000,1);
Log::d($baseAdapter->Relation->query( 10, 10 )->build()->paging());

Log::d($baseAdapter->fetchInstances());
?>