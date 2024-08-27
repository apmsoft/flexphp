<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\ColumnsEnum as Columns;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
// Log::init();
Log::init(Log::MESSAGE_ECHO);

// Log::options([
//     'datetime'   => false, # 날짜시간 출력여부
//     'debug_type' => true, # 디버그 타입 출력여부
//     'newline'    => true  # 개행문자 출력여부
// ]);

# resource
R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns/columns.json', 'column');

# 전체 구조 출력
Log::d( "cases ==>", Columns::cases());

# 이름만 배열로 받기
Log::d( 'names ==>',Columns::names());
Log::d( 'values ==>', Columns::values());
Log::d( 'array ==>',Columns::array());

Log::d(
    'column->name : '.Columns::NAME->name,
    'column->value : '.Columns::NAME->value,
    'column->label : '.R::column(Columns::NAME->value)
);
Log::d('>>>>>****',Columns::ID());

# 전체
Log::d(
    "key => value",
    Columns::byName('name')
);

Log::d(
    "default",
    Columns::byName('name')->name,
    Columns::byName('name')->value
);

Log::d(
    "대문자",
    Columns::byName('name',case:'upper')->name,
    Columns::byName('name',case:'upper')->value
);

Log::d(
    "소문자",
    Columns::byName('muid',case:'lower')->name,
    Columns::byName('muid',case:'lower')->value
);

Log::d(
    "변환없음",
    Columns::byName('Total',case:'none')->name,
    Columns::byName('Total',case:'none')->value
);


Log::d(
    "#대문자",
    Columns::NAME('upper')
);

Log::d(
    "#소문자",
    Columns::muid('lower')
);

Log::d(
    "#변환없음",
    Columns::Total('none')
);


Log::d(
    'column->name : '.Columns::NAME->value,
    'column->type : '.Columns::NAME->type()
);
?>