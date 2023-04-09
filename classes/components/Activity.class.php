<?php 
namespace Flex\Components;

use Flex\Annona\R;
use Flex\Annona\Log;

use Flex\Components\Data\Model\ListInterface;
use Flex\Components\Data\Model\ViewInterface;
use Flex\Components\Data\Model\PostInterface;
use Flex\Components\Data\Model\EditInterface;
use Flex\Components\Data\Model\ReplyInterface;
use Flex\Components\Data\Model\DeleteInterface;

use Flex\Annona\Request\FormValidation;
use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;

class Activity extends DbMySqli implements ListInterface,ViewInterface,PostInterface,EditInterface,DeleteInterface,ReplyInterface
{
    public function __construct(
        private string $T
    ){
        parent::__construct();
    }

    public function queryInert(array $params) : void 
    {
        parent::autocommit(FALSE);
        try{
            foreach($params as $column_name => $column_value){
                parent::offsetSet($column_name,$column_value);
            }
            parent::table( $this->T )->insert();
        }catch(\Exception $e){
            throw new \Exception ( $e->getMessage());
        }
        parent::commit();
    }

    #@ListInterface
    public function doList() : array 
    {}

    #@ViewInterface
    public function doView() : array
    {}

    #@PostInterface
    public function doPost() : array
    {}

    public function doInsert() : array
    {}

    #@EditInterface
    public function doEdit() : array
    {}

    public function doUpdate() : array
    {}

    #@ReplyInterface
    public function doReply() : array
    {}

    public function doRepl() : array
    {}

    #@DeleteInterface
    public function doDelete() : array
    {}
}
?>