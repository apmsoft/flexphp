<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Schema\TablesEnum;
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
Log::d( TablesEnum::names());
Log::d( TablesEnum::values());
Log::d( TablesEnum::array());
Log::d("==========");

Log::d(
    'column name : '.TablesEnum::MEMBER->name,
    'column name : '.TablesEnum::MEMBER->value,
    'column label : '.TablesEnum::MEMBER->label()
);
Log::d("==========");

# 이름에 해당하는 name,label 배열로 받기
Log::d( TablesEnum::fetchByName(TablesEnum::MEMBER->name) );
Log::d("==========");

# 전체
Log::d( TablesEnum::fetchAll() );
?>