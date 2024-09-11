<?php
use Flex\Annona\App;
use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\Example\ExampleEnum;
use Flex\Annona\Request\FormValidation as Validation;
use Flex\Annona\Array\ArrayHelper;
use Flex\Components\Adapter\DbMysqlAdapter;
use Flex\Components\Data\Action\ListInterface;
use Flex\Annona\Model;
use Flex\Annona\Json\JsonEncoder;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
// Log::init();
Log::init(Log::MESSAGE_ECHO);

# resource
R::tables();

# ExampleEnum 인스턴스 생성
$example = ExampleEnum::create();

// 값 설정
$example->setId(1);
$example->setTitle("Text");
$example->setSigndate("2023-09-04 10:00:00");

// 값 가져오기
Log::d( "ID: " . $example->getId() );
Log::d( "Name: " . $example->getTitle() );
Log::d( "Sign Date: " . $example->getSigndate() );

// 특정 형식으로 날짜 가져오기
Log::d( "Formatted Sign Date: " . $example->getSigndate("Y-m-d") );

// 모든 설정된 값 가져오기
Log::d($example->getInstanceValues());

// Enum의 기본 값들 가져오기
Log::d(ExampleEnum::values());

// Enum의 이름들 가져오기
Log::d(ExampleEnum::names());

// Enum의 이름과 값을 연관 배열로 가져오기
Log::d(ExampleEnum::array());

// byName 메서드 사용
$idCase = ExampleEnum::byName('ID');
Log::d( "ID case: " . $idCase->name . " - " . $idCase->value );

// 값 초기화
ExampleEnum::resetValues();

// 초기화 후 값 확인
Log::d($example->getInstanceValues());

// 메서드 체이닝 사용
$example->setId(2)
        ->setTitle("Jane Smith");

Log::d( "After chaining - ID: " . $example->getId() . ", Name: " . $example->getTitle() );

# example class
class Test extends DbMysqlAdapter implements ListInterface 
{
    private ExampleEnum $exampleEnum;
    public function __construct() {
        # 방법1 (WhereHelper 클래스 자동 선언됨)
        parent::__construct(new DbMySqli());

        $this->exampleEnum = ExampleEnum::create();

        # 방법2
        #parent::__construct(new DbMySqli(), new MyCustumWhereHelper());
    }

    public function doList(?array $params=[]) : ?string
    {
        # model
        $model = new Model();
        $model->data = [];

        # query
        $result = $this->db->table("test")
            ->select(
                ExampleEnum::ID(),
                ExampleEnum::TITLE(),
                ExampleEnum::SIGNDATE(),
            )
            ->where( ExampleEnum::ID() ,">=",10 )
            ->query();

        while ($row = $result->fetch_assoc()())
        {
            // array push
            $model->data[] = [
                ExampleEnum::ID()      => $this->exampleEnum->setId( (int)$row[ExampleEnum::ID()] )->getId(),
                ExampleEnum::TITLE()   => $this->exampleEnum->setTitle( $row[ExampleEnum::TITLE()] )->getTitle(),
                ExampleEnum::SIGNDATE()=> $this->exampleEnum->setSigndate( $row[ExampleEnum::SIGNDATE()] )->getSigndate()
            ];
        }

        # output
        return JsonEncoder::toJson([
            "result" => "true",
            "msg"    => $model->data
        ]);
    }
}

#(new Test())->doList();
?>