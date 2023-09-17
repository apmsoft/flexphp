<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\ColumnsEnum;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(dirname(__DIR__));
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

// Log::options([
//     'datetime'   => false, # 날짜시간 출력여부
//     'debug_type' => true, # 디버그 타입 출력여부
//     'newline'    => true  # 개행문자 출력여부
// ]);

# resource
R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns/default.json', 'columns');

# 전체 구조 출력
// Log::d( "cases ==>", ColumnsEnum::cases());

// # 이름만 배열로 받기
Log::d( 'names ==>',ColumnsEnum::names());
Log::d( 'values ==>', ColumnsEnum::values());
Log::d( 'array ==>',ColumnsEnum::array());

Log::d(
    'column name : '.ColumnsEnum::NAME->name,
    'column value : '.ColumnsEnum::NAME->value,
    'column label : '.R::columns(ColumnsEnum::NAME->value)
);


# 전체
Log::d(
    ColumnsEnum::byName('name')['name'],
    ColumnsEnum::byName('name')['value']
);

?>