<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Columns\ColumnsEnum;
use Flex\ColumnsTypes\ColumnsTypes;
use Flex\Annona\Request\FormValidation as Validation;

use Flex\Components\Adapter\DbBaseAdapter;
use Flex\Components\Data\Action\ListInterface;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
// Log::init();
Log::init(Log::MESSAGE_ECHO);

// Log::options([
//     'datetime'   => false, # 날짜시간 출력여부
//     'debug_type' => true, # 디버그 타입 출력여부
//     'newline'    => true  # 개행문자 출력여부
// ]);

# resource
// R::parser(_ROOT_PATH_.'/'._QUERY_.'/columns/columns.json', 'column');

# 단일 setter, getter
$id       = (new ColumnsTypes())->setId(1)->getId();
$userId   = (new ColumnsTypes())->setUserId('ddd@naver.com')->getUserId();
$signdate = (new ColumnsTypes())->setSigndate(date('Y-m-d H:i:s'))->getSigndate('Y.m.d');
Log::d( 'id',$id,  'userId',$userId, 'signdate',$signdate);

# 멀티 셋
$multiset_values = (new ColumnsTypes())->setId(1)->setUserId('ddd@naver.com')->setSigndate(date('Y-m-d H:i:s'))->values();
Log::d( $multiset_values );

$columnsTypes = new ColumnsTypes();
$columnsTypes->setId(1)->setUserId('ddd@naver.com')->setSigndate(date('Y-m-d H:i:s'));
$multiget_values_v = $columnsTypes->getId(true)->getSigndate(chain:true)->values();
Log::d($multiget_values_v);


$multiget_values = (new ColumnsTypes())
    ->setId(1)
    ->setUserId('ddd@naver.com')
    ->setSigndate(date('Y-m-d H:i:s'))
    ->getId(true)
    ->getUserId(true)
    ->getSigndate('Y.m.d', true)
    ->values();

Log::d($multiget_values);

class Test extends DbBaseAdapter implements ListInterface 
{

    public function __construct() {}

    public function doList(?array $params=[]) : ?string
    {

        // try{
        //     (new Validation(ComColumn::PAGE(),R::strings(ComColumn::PAGE()),$this->requested->{ComColumn::PAGE()} ?? 1))->number();
        // }catch(\Exception $e){
        //     Log::e($e->getMessage());
        //     return $e->getMessage();
        // }

        # DB 클래스 호출
        // $this->db

        # total record
        // $total_record = $this->db->table(R::tables('driving_log'))->where($model->where)->total();

        # 페이징 클래스 호출
        /**
         * $paging = $this->relation(total_record :100, page :1, limit : 10, block:5);
         * $relaction = $this->relation;
         * $paging->page;
         * $paging->totalPage;
         * $paging->qLimitStart;
         * $paging->qLimitEnd;
         * */ 

         /**
          * # query
          * $rlt = $this->db->table(R::tables('driving_log'))->orderBy(Column::ID().' DESC')->limit($paging->qLimitStart, $paging->qLimitEnd)->query();

        *while($row = $rlt->fetch_assoc())
        *{
        *
        *    $model->data[] = (new ColumnsTypes())
            *    ->setId(1)
            *    ->setUserId( (int)$row[ColumnsEnum::ID()] )
            *    ->setSigndate( $row[ColumnsEnum::SIGNDATE() )
            *    ->getId(true)
            *    ->getUserId(true)
            *    ->getSigndate(chain:true)
            *    ->values();
        *}
          */

        return "";
    }
}
?>