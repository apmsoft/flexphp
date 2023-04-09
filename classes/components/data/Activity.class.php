<?php 
namespace Flex\Components\Data;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;

class Activity extends DbMySqli
{
    public function __construct(
        private string $T
    ){
        parent::__construct();
    }

    # 데이터 저장
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

    # 데이터 업데이트
    public function queryUpdate(array $params, array $where) : void 
    {
        parent::autocommit(FALSE);
        try{
            foreach($params as $column_name => $column_value){
                parent::offsetSet($column_name,$column_value);
            }
            parent::table( $this->T )->where($where)->update();
        }catch(\Exception $e){
            throw new \Exception ( $e->getMessage());
        }
        parent::commit();
    }
}
?>