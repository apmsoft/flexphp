<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Components\DataProcessing;

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

# 이름에 해당하는 name,label 배열로 받기
Log::d(
    (new DataProcessing())
    ->setByName(ColumnsEnum::NAME->value, '홍길동')
    ->setByName(ColumnsEnum::ID->value, 1)
    ->setByName(ColumnsEnum::PASSWD->value, "dafdsafa")
    ->setByName(ColumnsEnum::DESCRIPTION->value, "dsafa$#%#<a href=\"https://m.naver.com\">네이버</a>", "view", ["HTML"])
    ->fetchAll()
);
?>