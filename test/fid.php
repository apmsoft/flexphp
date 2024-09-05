<?php

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\Example\ExampleEnum;
use Flex\Components\Adapter\DbMysqlAdapter;
use Flex\Components\Data\Action\ListInterface;
use Flex\Annona\Model;
use Flex\Annona\Json\JsonEncoder;
use Flex\Components\Data\Action\InsertInterface;
use Flex\Components\Data\Mgmt\FidProviderInterface;
use Flex\Components\Data\Mgmt\FidTrait;
use Flex\Annona\Date\DateTimez;

# config
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_ECHO);

# resource
R::tables();
$db = new DbMySqli();

# example class
class Test extends DbMySqlAdapter implements ListInterface,InsertInterface,FidProviderInterface
{
    use FidTrait;

    # 퀄럼 instance
    private ExampleEnum $exampleEnum;

    public function __construct(DbMySqli $db)
    {
        # 방법1 (WhereHelper 클래스 자동 선언됨)
        parent::__construct( $db );

        # 퀄럼instance
        $this->exampleEnum = ExampleEnum::create();
    }

    #@ interface ,FidProviderInterface
    public function getTable(): string
    {
        return R::tables('notice');
    }

    #@ interface ,FidProviderInterface
    public function getFidColumnName(): string{
        return ExampleEnum::FID();
    }

    #@ ListInterface
    public function doList(?array $params=[]) : ?string
    {
        # model
        $model = new Model();
        $model->data = [];

        # query
        $result = $this->db->table( R::tables('notice') )
            ->select(
                ExampleEnum::ID(),
                ExampleEnum::TITLE(),
                ExampleEnum::SIGNDATE(),
                ExampleEnum::FID(),
            )
            ->orderBy( $this->orderBy() )
            ->limit(10)
            ->query();

        while ($row = $result->fetch_assoc())
        {
            # set/get
            $row[ExampleEnum::ID()]       = $this->exampleEnum->setId( (int)$row[ExampleEnum::ID()] )->getId();
            $row[ExampleEnum::TITLE()]    = $this->exampleEnum->setTitle( $row[ExampleEnum::TITLE()] )->getTitle();
            $row[ExampleEnum::SIGNDATE()] = $this->exampleEnum->setSigndate( $row[ExampleEnum::SIGNDATE()] )->getSigndate();
            $row[ExampleEnum::FID()]      = $this->exampleEnum->setFid( $row[ExampleEnum::FID()] )->getFid();
            $row['depth'] = $this->getDepthCount( $row[ExampleEnum::FID()] );

            // array push
            $model->data[] = $row;
        }

        # output
        return JsonEncoder::toJson([
            "result" => "true",
            "msg"    => $model->data
        ]);
    }

    #@ InsertInterface
    public function doInsert(?array $params=[]) : string 
    {
        # db
        $this->db->autocommit(FALSE);
        try{
            $this->db[ExampleEnum::TITLE() ] = sprintf("테스트 %d", time());
            $this->db[ExampleEnum::SIGNDATE() ] = (new DateTimez("now"))->format("Y-m-d H:i:s");
            $this->db[ExampleEnum::FID() ] = $this->createParentFid();

            $this->db->table( R::tables('notice') )->insert();
        }catch (\Exception $e){
            Log::e( $e->getMessage() );
        }
        $this->db->commit();

        # output
        return JsonEncoder::toJson([
            "result" => "true",
            "msg"    =>R::sysmsg('v_insert')
        ]);
    }
}

(new Test( $db ))->doInsert();
Log::v( json_decode( (new Test( $db ))->doList(), true) );
?>