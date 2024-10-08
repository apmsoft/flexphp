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

$db = new DbMySqli();

// $query = $db->table( R::tables('member') )->query;
// Log::d($query);
// $data = $db->table( R::tables('member') )->query()->fetch_assoc();
// Log::d($data);

$rlt = $db->table( R::tables('member') )->query();
while($row = $rlt->fetch_assoc()){
    Log::d($row);
}
$rlt->free();

$query = $db->table( R::tables('member') )->select('id','name','userid')->query;
Log::d($query);
$data = $db->table( R::tables('member') )->select('id','name','userid')->query()->fetch_assoc();
Log::d('instance',$data);
$db->free();

$rlt = (new DbMySqli())->table( R::tables('member') )->select('id','name','userid')->limit(10)->query();
while($row = $rlt->fetch_assoc()){
    print_r($row);
}
$rlt->free();

// $query = $db->table( R::tables('member') )->where('id',1)->query;
// Log::d($query);
// $data = $db->table( R::tables('member') )->where('id',1)->query()->fetch_assoc();
// Log::d($data);
// Log::d('direct',(new DbMySqli())->table( R::tables('member') )->where('id',1)->query()->fetch_assoc() );

// $query = $db->table( R::tables('member') )->select('id','name','userid')->where('id','>',1)->query;
// Log::d($query);
// $rlt = $db->table( R::tables('member') )->select('id','name','userid')->where('id',1)->orderBy('id desc','name asc')->limit(3)->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }
// $rlt->free();

# 총 레코드 수
// $total = $db->table( R::tables('member') )->total();
// $total = $db->table( R::tables('member') )->where('name','LIKE-R','김')->total();
// Log::d('TOTAL',$total);

# ================/==============
# *******************************
#######[ SUB QUERY ] ##########
# *******************************
# ===============================
# ************
# SELECT id, name,userid FROM flex_member WHERE (id IN (SELECT muid FROM flex_coupon_numbers))
# tableSub
#--------------
$rlt = $db->table( R::tables('member') )->select("id","name","userid")->where(
    sprintf("id IN (%s)", $db->tableSub($tables->coupon_numbers)->select("muid")->query)
)->query();
while($row = $rlt->fetch_assoc()){
    print_r($row);
}
$rlt->free();

$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc()){
    print_r($row);
}
$rlt->free();

###
### tableSub in select,
### tableSub in where
###
$rlt = $db->table( R::tables('member') )->select("id","name",
    sprintf("(%s) as cnt", $db->tableSub($tables->coupon_numbers)->select("count(*)")->where('muid','>','0')->query)
)->where(
    sprintf("id IN (%s)", $db->tableSub($tables->coupon_numbers)->select("muid")->query)
)->query();
// Log::d($rlt);

while($row = $rlt->fetch_assoc()){
    print_r($row);
}
$rlt->free();


# ================/==============
# *******************************
#######[ Group By ] ##########
# *******************************
# ===============================

# Group By
// $query = $db->table( R::tables('member') )->selectGroupBy('id','name','signdate')->groupBy('`name`')->limit(0,10)->query;
// Log::d('Group By',$query);

$query = $db->table( R::tables('member') )->selectGroupBy('id,name,signdate')->groupBy('name')->limit(0,10)->query;
Log::d('Group By',$query);
// $rlt = $db->table( R::tables('member') )->selectGroupBy('id','name','signdate')->groupBy('`name`')->orderBy('name asc')->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# Group By HAVING
// $query = $db->table( R::tables('member') )->selectGroupBy('id','userid','count(id) as cnt','signdate')->groupBy('userid')->having('id','>','0')->query;
// Log::d('Group By HAVING',$query);
// $rlt = $db->table( R::tables('member') )->selectGroupBy('count(signdate) as cnt','signdate')->groupBy('signdate')->having('signdate','>','0')->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }


# ================/==============
# *******************************
#######[ JOIN ] ##########
# *******************************
# ===============================

# JOIN
// $query = $db->table("{ R::tables('member') } m", "{$tables->coupon_numbers} cn")
// ->where('m.id','cn.muid')
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')->query;
// Log::d('join',$query);

// $rlt = $db->table("{ R::tables('member') } m","{$tables->coupon_numbers} cn")
// ->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
// ->where('m.id','cn.muid')
// ->limit(3)
// ->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }

# JOIN
$rlt = $db->tableJoin("INNER","{ R::tables('member') } m","{$tables->coupon_numbers} cn")
->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
->on('m.id','cn.muid')
->limit(3)
->query();
while($row = $rlt->fetch_object()){
    // print_r($row);
    Log::d('object',$row->id, $row->userid, $row->coupon_number, $row->cid);
}

# LEFT
# INNER|LEFT|RIGHT|LEFT OUTTER|RIGHT OUTTER JOIN
$query = $db->tableJoin("LEFT", "{ R::tables('member') } m", "{$tables->coupon_numbers} cn")
->on('m.id','cn.muid')
->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
->query;
Log::d('join',$query);

# INNER
$rlt = $db->tableJoin("INNER","{ R::tables('member') } m","{$tables->coupon_numbers} cn")
->select('m.id','m.userid','cn.coupon_number','cn.id as cid')
->on('m.id','cn.muid')
->where("m.id", ">",30)
->limit(3)
->query();
while($row = $rlt->fetch_assoc()){
    print_r($row);
}

# UNION
$rlt = $db->tableJoin("UNION",
    $db->tableSub( R::tables('member') )->select('id','name','userid')->query,
    $db->tableSub( R::tables('member') )->select('id','name','userid')->query
)->where('id', '>', 2)->limit(10)->query();
// Log::d($rlt);
while($row = $rlt->fetch_assoc()){
    Log::d($row);
}
$rlt->free();

# ================/==============
# *******************************
#######[ INSERT, UPDATE, DELETE ] ##########
# *******************************
# ===============================

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
    # UPDATE `flex_coupon` SET uses_number=uses_number+1 WHERE `id`='1'
    # 사용횟수 업데이트
    // $this->db['uses_number'] = 'uses_number+1';
    // $this->db->table($tables->coupon)->where('id', '1')->update();
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


# ================/==============
# *******************************
#######[ 암호화 ] ##########
# *******************************
# ===============================

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

// $query_string = $db->table( R::tables('member') )->query;
// $data2 = $db->query( $query_string )->fetch_assoc();
// Log::d($data2);


# ================/==============
# *******************************
#######[ WhereHelper ] ##########
# *******************************
# ===============================
// $query = $db->table( R::tables('member') )->select('id','name','userid')->where(
//     (new WhereHelper)->
//         begin('OR')->case('name','LIKE','김')->case('userid', 'LIKE-L', '@gmail.com')->end()
//         begin('AND')->case('signdate','>=','2002-12-12')->case('level', '>', '0')->end()
//     ->where
// )->query;
// Log::d($query);

// $rlt = $db->table( R::tables('member') )->select('id','name','userid')->where(
//     (new WhereHelper)->
//         begin('OR')->case('name','LIKE','김')->case('userid', 'LIKE-L', '@gmail.com')->end()
//         begin('AND')->case('signdate','>=','2002-12-12')->case('level', '>', '0')->end()
//     ->where
// )->orderBy('id desc','name asc')->limit(3)->query();
// while($row = $rlt->fetch_assoc()){
//     print_r($row);
// }
?>