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

$baseAdapter->add(Model::class, []);
$baseAdapter->Model->a = 1;
$baseAdapter->Model->b = 4;
$baseAdapter->Model->data = [];
$baseAdapter->Model->{"data+"} = 1;
$baseAdapter->Model->{"data+"} = 2;
$baseAdapter->Model->{"data+"} = 3;
Log::d($baseAdapter->Model->fetch());

$baseAdapter->add(ArrayHelper::class, [["a"=>"a"],["a"=>"b"]]);
Log::d($baseAdapter->ArrayHelper->sorting('a','DESC')->value);

$baseAdapter->add(UuidGenerator::class);
Log::d($baseAdapter->UuidGenerator->v4());
Log::d($baseAdapter->its('UuidGenerator')->v4());


$baseAdapter->add('Flex\Annona\Paging\Relation', 1000,1);
Log::d($baseAdapter->Relation->query( 10, 10 )->build()->paging());

Log::d($baseAdapter->fetchInstances());
?>