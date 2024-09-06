<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\Example\ExampleEnum;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_ECHO);

# 전체 구조 출력
Log::d( "cases ==>", ExampleEnum::cases());

# 이름만 배열로 받기
Log::d( 'names ==>',ExampleEnum::names());
Log::d( 'values ==>', ExampleEnum::values());
Log::d( 'array ==>',ExampleEnum::array());

Log::d(
    'column->name : '.ExampleEnum::TITLE->name,
    'column->value : '.ExampleEnum::TITLE->value,
    'column->label : '.R::column(ExampleEnum::TITLE->value)
);
Log::d('>>>>>****',ExampleEnum::ID());

# 전체
Log::d(
    "key => value",
    ExampleEnum::byName('name')
);

Log::d(
    "default",
    ExampleEnum::byName('name')->name,
    ExampleEnum::byName('name')->value
);

Log::d(
    "대문자",
    ExampleEnum::byName('name',case:'upper')->name,
    ExampleEnum::byName('name',case:'upper')->value
);

Log::d(
    "소문자",
    ExampleEnum::byName('muid',case:'lower')->name,
    ExampleEnum::byName('muid',case:'lower')->value
);

Log::d(
    "변환없음",
    ExampleEnum::byName('Total',case:'none')->name,
    ExampleEnum::byName('Total',case:'none')->value
);


Log::d(
    "#대문자",
    ExampleEnum::TITLE('upper')
);

Log::d(
    "#소문자",
    ExampleEnum::muid('lower')
);

Log::d(
    "#변환없음",
    ExampleEnum::Total('none')
);

?>