<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Validation\Validation;

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
R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns.json', 'columns');

try{
    (new Validation())->name('홍길동')->userid('');
}catch(\Exception $e) {
    Log::e($e->getFile(), $e->getLine(), $e->getMessage());
}


?>