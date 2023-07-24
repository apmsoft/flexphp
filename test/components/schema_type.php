<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Components\Schema\SchemaType;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(dirname(__DIR__));
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# resource
R::parser(_ROOT_PATH_.'/'._CONFIG_.'/components/components.json', 'components');


try {
    Log::d((new SchemaType( R::components('columns') ))->fetchByName(ColumnsEnum::NAME->name));
    Log::d((new SchemaType( R::components('columns') ))->fetchByName(ColumnsEnum::SIGNDATE->name));
} catch (\UnexpectedValueException $e) {
    Log::e($e->getMessage() );
}

# 전체
Log::d(
    (new SchemaType( R::components('columns') ))->fetchAll()
);
?>