<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

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
$tables = R::dic(R::$tables[R::$language]);

/**
 * define('_DB_HOST_','127.0.0.1');
 * define('_DB_HOST_','test2');
 * define('_DB_PASSWD_','d1004');
 * define('_DB_NAME_','test_db2');
 * define('_DB_PORT_',33060);
 */
# db
// $db = new \Flex\Annona\Db\MySqli();
$db = new \Flex\Annona\Db\DbMySqli('localhost:mysql', 'root', 'd1004', 8080);

$query = $db->table($tables->member)->query;
#$query = $db->table($tables->member)->query()->fetch_assoc();
Log::d($query);

$query = $db->table($tables->member)->select('id','name','userid')->query;
#$query = $db->table($tables->member)->select('id','name','userid')->query()->fetch_assoc();
Log::d($query);

$query = $db->table($tables->member)->where('id',1)->query;
#$query = $db->table($tables->member)->where('id',1)->query()->fetch_assoc();
Log::d($query);

$query = $db->table($tables->member)->select('id','name','userid')->where('id',1)->query;
Log::d($query);

$query = $db->table($tables->member)->select('id','name','userid')->where('id','>',1)->query;
Log::d($query);

$query = $db->table($tables->member)->select('id','name','userid')->where('id','IN',['공무원','프로그래머','경영인','디자이너'])->query;
Log::d($query);

$query = $db->table($tables->member)->where('id',1)->orderBy('id', 'DESC')->query;
Log::d($query);

$query = $db->table($tables->member)->where('id',1)->orderBy('id', 'DESC')->limit(0,10)->query;
// $query = $db->table($tables->member)->where('id',1)->orderBy('id', 'DESC')->limit(0,10)->query();
Log::d($query);

$query = $db->table($tables->member)->where('id',1)->orderBy('id', 'DESC')->limit(10)->query;
// $query = $db->table($tables->member)->where('id',1)->orderBy('id', 'DESC')->limit(10)->query();
Log::d($query);

# loop
try{
    $query = $db->table($tables->member)->orderBy('id', 'DESC')->limit(10)->query;
    // $result = $db->table($tables->member)->orderBy('id', 'DESC')->limit(10)->query();
    // while($row = $result->fetch_assoc()){
    //     Log::d($row);
    // }
    Log::d($query);
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 단일 레코드
try{
    #$record_info = $db->table($tables->member)->where('id', '1')->query()->fetch_assoc();
    Log::d($record_info);
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# total recrod
#$total = $db->table($tables->member)->where('name','LIKE','김')->total();
#Log::d('total',$total);

# order by multi
$query = $db->table($tables->member)->where('')->orderBy('id','DESC','signdate','ASC')->limit(10)->query;
Log::d('fetch_assoc',$query);

# WhereHelper
# db where 구문 만들기
$whereHelper = new \Flex\Annona\Db\WhereHelper();
$whereHelper->beginWhereGroup('groupa', 'AND');
    $whereHelper->setBuildWhere('name', 'IN' , '홍길동,유관순', true);
    $whereHelper->setBuildWhere('age', '>=' , 10, true);
    $whereHelper->setBuildWhere('job', 'IN' , ['공무원','프로그래머','경영인','디자이너'], true);
    $whereHelper->setBuildWhere("JSON_UNQUOTE(detail_info->'$.deli.dome.type')", 'LIKE-R', "d" ,true,false);
    $whereHelper->setBuildWhere("signdate", 'is not', 'NULL' ,true,false);
    $whereHelper->setBuildWhere('price', '>', '0',true);
$whereHelper->endWhereGroup();

$query = $db->table($tables->member)->where( $whereHelper->where )->orderBy('id','DESC')->limit(0,10)->query;
Log::d('whereHelper',$query);

# Group By
$query = $db->table($tables->member)->selectGroupBy('id','name','signdate')->groupBy('`name`')->limit(0,10)->query;
Log::d('Group By',$query);

# Group By WHERE
$query = $db->table($tables->member)->selectGroupBy('id','name','signdate')->where('id','>',1)->groupBy('`name`')->limit(0,10)->query;
Log::d('Group By',$query);

# Group By HAVING
$query = $db->table($tables->member)->selectGroupBy('id','userid','count(name) as cnt','signdate')->where('id','>',1)->groupBy('userid')->having('id','>','0')->query;
Log::d('Group By HAVING',$query);

# insert
try{
    $db['id']       = 1;
    $db['name']     = '홍길동'.time();
    $db['signdate'] = date('Y-m-d H:i:s');
    $db->table($tables->member)->insert();
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# update
try{
    $db['name'] = '홍길동M'.time();
    $db->table($tables->member)->where('id', 1)->update();
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# delete
$db->table($tables->member)->where('id', 1)->delete();
?>
