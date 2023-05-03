<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Components\Validation;

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

try{
    (new Validation( R::components('columns')) )
    ->is(ColumnsEnum::ID->name, "1")
    ->is(ColumnsEnum::NAME->name,"홍길동##")
    ->is(ColumnsEnum::EMAIL->name,"test@ddd.com")
    ->is(ColumnsEnum::EXTRACT_DATA->name,'[]');
}catch(\Exception $e) {
    Log::e($e->getFile(), $e->getLine(), $e->getMessage());
}
?>