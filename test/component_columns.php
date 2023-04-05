<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Component\Columns;
use Flex\Annona\Array\ArrayHelper;

$path = dirname(__DIR__);
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
R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns.json', 'columns');

Log::d(
    'column name : '.Columns::NAME->name(),
    'column label : '.Columns::NAME->label(),
    'column type : '.Columns::NAME->type(),
    'column 길이 : '.Columns::NAME->length(),
    '데이터 타입 : '.Columns::NAME->valueType()
);

# 전체 구조 출력
Log::d(Columns::cases());

// # 이름만 배열로 받기
Log::d( Columns::names());

# 이름에 해당하는 name,type,length,valueType 배열로 받기
Log::d( Columns::fetchByName('ID'));

# 전체 타일
foreach(Columns::names() as $_NAME) {
    Log::d(Columns::fetchByName( $_NAME ));
}
?>