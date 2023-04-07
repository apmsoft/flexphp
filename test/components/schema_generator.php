<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Schema\SchemaGenerator;
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
R::parser(_ROOT_PATH_.'/'._CONFIG_.'/components/components.json', 'components');

# 이름에 해당하는 name,label 배열로 받기
try {
    Log::d(
        (new SchemaGenerator('notice', '공지사항'))
        ->columns('id','name','title','signdate','muid','description')
        ->primaryKey('id')
        ->indexKey(['name'=>'name','ms'=>'muid,signdate'])
        // ->engine('InnoDB')
        // ->charset('utf8')
        // ->auto_increment(100)
        ->create()
    );
} catch (UnexpectedValueException $e) {
    echo $e->getMessage() . PHP_EOL;
}

?>