<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\ColumnsEnum as Column;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(__DIR__);
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
R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns/columns.json', 'column');

# 전체 구조 출력
// Log::d( "cases ==>", Column::cases());

// # 이름만 배열로 받기
Log::d( 'names ==>',Column::names());
Log::d( 'values ==>', Column::values());
Log::d( 'array ==>',Column::array());

// Log::d(
//     'column->name : '.Column::NAME->name,
//     'column->value : '.Column::NAME->value,
//     'column->label : '.R::column(Column::NAME->value)
// );
Log::d('>>>>>****',Column::ID());

# 전체
Log::d(
    "key => value",
    Column::byName('name')
);

Log::d(
    "default",
    Column::byName('name')->name,
    Column::byName('name')->value
);

Log::d(
    "대문자",
    Column::byName('name',case:'upper')->name,
    Column::byName('name',case:'upper')->value
);

Log::d(
    "소문자",
    Column::byName('muid',case:'lower')->name,
    Column::byName('muid',case:'lower')->value
);

Log::d(
    "변환없음",
    Column::byName('Total',case:'none')->name,
    Column::byName('Total',case:'none')->value
);

?>