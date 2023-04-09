<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Components\Schema\TablesEnum;
use Flex\Components\Validation;
use Flex\Components\Data\Model\ListInterface;

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

class TestList extends ListActivityAbstract
{
    public function __construct(
        private string $T,
        array $request_params
    ){
        parent::__construct($this->T, $request_params);
    }

    #@ Abstract
    public function doList() : array
    {
        try{
            Log::d(
                parent::init()->validation()->totalRecord()->pageRelation()->runQuery("id,title,signdate","")->putOutData(["r" =>[]])->extract()
            );
        }catch (\UnexpectedValueException $e) {
            throw new \UnexpectedValueException( $e->getMessage() );
        }catch (\Exception $e) {
            throw new \Exception( $e->getMessage() );
        }
    }
}

$bbs_notice = (new TestList( TablesEnum::BBS_NOTICE->value, (new Request())->get()->fetch() ) )->doList();
?>