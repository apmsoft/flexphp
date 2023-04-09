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
use Flex\Components\DataProcessing;

use Flex\Annona\Request\FormValidation;
use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;


class Activity extends DbMySqli
{
    public function __construct(
        private string $T
    ){
        parent::__construct();
    }

    // # 데이터 루프쿼리
    // public function queryFetchAll(mysqli_result $result) : array 
    // {
    //     $dataProcessing = new DataProcessing();
    //     while( $row =  $result->fetch_assoc())
    //     {
    //         foreach($row as $column_name => $column_value)
    //         {
    //             try{
    //                 $dataProcessing->put($column_name, $column_value);
    //             }catch (\UnexpectedValueException $e) {
    //                 throw new \UnexpectedValueException( $e->getMessage() );
    //             }catch (\Exception $e) {
    //                 throw new \Exception( $e->getMessage() );
    //             }
    //         }
    //     }
    //     $result->free();

    // return $dataProcessing->fetchAll();
    // }

    // # 데이터 저장
    // public function queryInert(array $params) : void 
    // {
    //     parent::autocommit(FALSE);
    //     try{
    //         foreach($params as $column_name => $column_value){
    //             parent::offsetSet($column_name,$column_value);
    //         }
    //         parent::table( $this->T )->insert();
    //     }catch(\Exception $e){
    //         throw new \Exception ( $e->getMessage());
    //     }
    //     parent::commit();
    // }

    // # 데이터 업데이트
    // public function queryUdate(array $params, array $where) : void 
    // {
    //     parent::autocommit(FALSE);
    //     try{
    //         foreach($params as $column_name => $column_value){
    //             parent::offsetSet($column_name,$column_value);
    //         }
    //         parent::table( $this->T )->where($where)->update();
    //     }catch(\Exception $e){
    //         throw new \Exception ( $e->getMessage());
    //     }
    //     parent::commit();
    // }
}
?>