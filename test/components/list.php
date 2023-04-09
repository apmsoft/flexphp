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

class TestList extends Actvity implements ListInterface 
{
    public function __construct(
        private string $T
    ){
        parent::__construct($this->T);
    }

    #@ ListInterface
    public function doList() : array
    {
        # request

        # validation

        # query
        $loop = [];
        $result = parent::table($this->T)->query();
        while( $row = $result->fetch_assoc())
        {
            $loop = (new DataProcessing($row))
            ->put(ColumnsEnum::DESCRIPTION->name, $row[ColumnsEnum::DESCRIPTION->value], "view", ["HTML"])
            ->fetchAll();
        }
        $result->free();

        # output
    }
}

$bbs_notice = (new TestList(TablesEnum::BBS_NOTICE->value))->doList();
?>