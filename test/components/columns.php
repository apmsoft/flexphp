<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Columns\ColumnsEnum;
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

# 전체 구조 출력
// Log::d(ColumnsEnum::cases());

// # 이름만 배열로 받기
Log::d( ColumnsEnum::names());
Log::d( ColumnsEnum::values());
Log::d( ColumnsEnum::array());
Log::d("==========");

Log::d(
    'column name : '.ColumnsEnum::NAME->name,
    'column name : '.ColumnsEnum::NAME->value,
    'column label : '.ColumnsEnum::NAME->label()
);
Log::d("==========");

# 이름에 해당하는 name,label 배열로 받기
Log::d( ColumnsEnum::fetchByName(ColumnsEnum::ID->name) );
Log::d("==========");

# 전체
Log::d( ColumnsEnum::fetchAll() );
?>