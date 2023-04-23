<?php

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Schema\TablesMap;
use Flex\Annona\Array\ArrayHelper;
use Flex\Annona\Model;
use Flex\Components\Activity\Activity;
use Flex\Annona\Uuid\UuidGenerator;

$path = dirname(dirname(__DIR__));
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

$activity = new Activity();

# single
$activity->add(Model::class, []);
$activity->Model->b = 4;
Log::d($activity->Model->fetch());

$activity->add(ArrayHelper::class, [["a"=>"a"],["a"=>"b"]]);
Log::d($activity->ArrayHelper->sorting('a','DESC')->value);

$activity->add(UuidGenerator::class, []);
Log::d($activity->UuidGenerator->v4());
Log::d($activity->fetchInstances());

# multi
$activity->add(['Flex\Annona\Cipher\Encrypt', 'dafdsafdas'], ['Flex\Annona\Random\Random', []] );
Log::d( $activity->Encrypt->_md5());
Log::d( $activity->Random->array( 10 ));
?>