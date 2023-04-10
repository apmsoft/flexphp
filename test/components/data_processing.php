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
try{
    $dataAll = (new DataProcessing([
        'name' => '홍길동',
        'id' => 1
    ]))
        ->put(ColumnsEnum::EXTRACT_DATA->name, ["id"=>"1"],"json_encode")
        ->put(ColumnsEnum::PASSWD->name, "dafdsafa")
        ->put(ColumnsEnum::TITLE->name, "대만민국 국제 올림픽 대화에서", "cut", [10])
        ->put(ColumnsEnum::DESCRIPTION->name, "dsafa$#%#<a href=\"https://m.naver.com\">네이버</a>", "getContext", ["HTML"])
        ->fetchAll();
    
    Log::d($dataAll);
}catch (\UnexpectedValueException $e) {
    throw new \UnexpectedValueException( $e->getMessage() );
}catch (\Exception $e) {
    throw new \Exception( $e->getMessage() );
}
?>