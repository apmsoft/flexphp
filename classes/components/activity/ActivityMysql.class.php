<?php 
namespace Flex\Components\Activity;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;
use Flex\Components\Validation;

class ActivityMysql extends DbMySqli
{
    public function __construct(
        public string $Table
    ){
        parent::__construct();
    }

    # 밸리데이션 체크
    public function validation (array $params) : void 
    {
        $validation = new Validation();
        foreach($params as $key => $v){
            $validation->is($key, $v);
        }
    }

    # 데이터 저장
    public function queryInert(array $params) : void 
    {
        parent::autocommit(FALSE);
        try{
            foreach($params as $column_name => $column_value){
                parent::offsetSet($column_name,$column_value);
            }
            parent::table( $this->Table )->insert();
        }catch(\Exception $e){
            throw new \Exception ( $e->getMessage());
        }
        parent::commit();
    }

    # 데이터 업데이트
    public function queryUpdate(array $params, string $where) : void 
    {
        parent::autocommit(FALSE);
        try{
            foreach($params as $column_name => $column_value){
                parent::offsetSet($column_name,$column_value);
            }
            parent::table( $this->Table )->where($where)->update();
        }catch(\Exception $e){
            throw new \Exception ( $e->getMessage());
        }
        parent::commit();
    }
}
?>