<?php 
$path = dirname(__DIR__);
require $path . '/config/config.inc.php';

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use \Flex\Annona\Db\DbMySqli;


# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# R
R::init( App::$language );
R::tables();
R::tables(['test'=>'test']);
$tables = R::dic(R::$tables[R::$language]);

# 싱글 라인
$data = (new DbMySqli())->table($tables->member)->where('id',1)->query()->fetch_assoc();
Log::d('single line', $data);

$rlt = (new DbMySqli())->table($tables->member)->select('id','name','userid')->limit(10)->query();
while($row = $rlt->fetch_assoc()){
    print_r($row);
}

# 멀티
$db = new DbMySqli();

// $query = $db->table($tables->member)->query;
// Log::d($query);
$data = $db->table($tables->member)->query()->fetch_assoc();
Log::d($data);

// $rlt = $db->table($tables->member)->query();
// while($row = $rlt->fetch_assoc()){
//     Log::d($row);
// }

// $query = $db->table($tables->member)->select('id','name','userid')->query;
// Log::d($query);
// $data = $db->table($tables->member)->select('id','name','userid')->query()->fetch_assoc();
// Log::d('instance',$data);


// $query = $db->table($tables->member)->where('id',1)->query;
// Log::d($query);
// $data = $db->table($tables->member)->where('id',1)->query()->fetch_assoc();
// Log::d($data);

// $query = $db->table($tables->member)->select('id','name','userid')->where('id','>',1)->query;
// Log::d($query);
// $rlt = $db->table($tables->member)->select('id','name','userid')->where('id',1)->orderBy('id desc','name asc')->limit(3)->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# 총 레코드 수
$total = $db->table($tables->member)->total();
$total = $db->table($tables->member)->where('name','LIKE-R','김')->total();
Log::d('TOTAL',$total);

# Group By
// $query = $db->table($tables->member)->selectGroupBy('id','name','signdate')->groupBy('`name`')->limit(0,10)->query;
// Log::d('Group By',$query);
// $rlt = $db->table($tables->member)->selectGroupBy('id','name','signdate')->groupBy('`name`')->orderBy('name asc')->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# Group By HAVING
// $query = $db->table($tables->member)->selectGroupBy('id','userid','count(id) as cnt','signdate')->groupBy('userid')->having('id','>','0')->query;
// Log::d('Group By HAVING',$query);
// $rlt = $db->table($tables->member)->selectGroupBy('count(signdate) as cnt','signdate')->groupBy('signdate')->having('signdate','>','0')->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# JOIN
// $query = $db->table("{$tables->member} m", "{$tables->coupon_numbers} cn")
// ->where('m.id','cn.muid')
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')->query;
// Log::d('join',$query);

// $rlt = $db->table("{$tables->member} m","{$tables->coupon_numbers} cn")
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
// ->where('m.id','cn.muid')
// ->limit(3)
// ->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# INNER|LEFT|RIGHT|LEFT OUTTER|RIGHT OUTTER JOIN
// $query = $db->tableJoin("INNER", "{$tables->member} m", "{$tables->coupon_numbers} cn")
// ->on('m.id','cn.muid')
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
// ->query;
// Log::d('join',$query);

$rlt = $db->tableJoin("INNER","{$tables->member} m","{$tables->coupon_numbers} cn")
->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
->on('m.id','cn.muid')
->limit(3)
->query();
while($row = $rlt->fetch_object()){
    // print_r($row);
    Log::d('object',$row->id, $row->userid, $row->coupon_number, $row->cid);
}

# INNER|LEFT|RIGHT|LEFT OUTTER|RIGHT OUTTER JOIN
// $query = $db->tableJoin("LEFT", "{$tables->member} m", "{$tables->coupon_numbers} cn")
// ->on('m.id','cn.muid')
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
// ->query;
// Log::d('join',$query);

// $rlt = $db->tableJoin("INNER","{$tables->member} m","{$tables->coupon_numbers} cn")
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
// ->on('m.id','cn.muid')
// ->where("m.id", ">",30)
// ->limit(3)
// ->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# insert
// try{
//     $db['id']       = 1;
//     $db['name']     = '홍길동';
//     $db['signdate'] = time();
//     $db->table($tables->test)->insert();
// }catch(\Exception $e){
//     Log::e($e->getMessage());
// }

# insert
// try{
//     $db['id']       = 3;
//     $db['name']     = '이순신';
//     $db['signdate'] = time();
//     $db->table($tables->test)->insertEncrypt();
// }catch(\Exception $e){
//     Log::e($e->getMessage());
// }

# update
// try{
//     $db['name']     = '홍길동업';
//     $db->table($tables->test)->where('id',1)->update();
// }catch(\Exception $e){
//     Log::e($e->getMessage());
// }


// try{
//     $db['name']     = '유관순업';
//     $db->table($tables->test)->where('id',2)->updateEncrypt();
// }catch(\Exception $e){
//     Log::e($e->getMessage());
// }

# delete
// if($db->table($tables->test)->where('id',1)->delete()){
//     Log::d('삭제성공 id = 1');
// }


# 암호호 데이터 쿼리
// $query = $db->table($tables->test)->selectCrypt('id','name','signdate')->where('id',2)->query;
// Log::d($query);
// $data = $db->table($tables->test)->selectCrypt('id','name','signdate')->where('id',2)->query()->fetch_assoc();
// Log::d($data);


// # 암호화된 필드 검색하기
// $query = $db->table($tables->test)->selectCrypt('id','name','signdate')->where($db->aes_decrypt('name'),'LIKE-R','유관순')->query;
// Log::d($query);
// $data = $db->table($tables->test)->selectCrypt('id','name','signdate')->where($db->aes_decrypt('name'),'LIKE-R','유관순')->query()->fetch_assoc();
// Log::d($data);

// $query_string = $db->table($tables->member)->query;
// $data2 = $db->query( $query_string )->fetch_assoc();
// Log::d($data2);
?>