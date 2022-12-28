<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Request\Request;
use Flex\Annona\Request\FormValidation;
use Flex\Annona\Model;
use Flex\Annona\Date\DateTimez;
use Flex\Annona\Date\DateTimezPeriod;

# config
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# Log
Log::init(Log::MESSAGE_FILE, sprintf("%s/log%s.out",_DATA_,date('Ymd')));
Log::setDebugs('i','d','v','e');
Log::options([
    'datetime'   => true,
    'debug_type' => true,
    'newline'    => true
]);

# request
$request = (object)(new Request())->post()->fetch();

# Form Validation
try{
    (new FormValidation('id','식별번호',$request->id))->null()->number();
}catch(\Exception $e){
    Log::e($e->getMessage());
    return json_decode($e->getMessage(),true);
}

# resource
R::tables();
R::array();
$sysmsg = R::dic(R::$sysmsg[R::$language]);
$tables = R::dic(R::$tables[R::$language]);
$array  = R::dic(R::$array[R::$language]);

# Database
$db = new DbMySqli();

# 데이터 체크
$data = new Model(
    $db->table($tables->member)->where('id',$request->id)->query()->fetch_assoc()
);
if(!isset($data->id)){
    return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>$sysmsg->e_db_unenabled];
}

# 데이터 처리
$period = (new DateTimezPeriod())->diff(date('Y-m-d H:i:s'), $data->signdate, ["format"=>'top']);
$snsf   = explode(' ', $period);
$data->signdate = match($snsf[1]) {
    'second','seconds' => sprintf("%d 초전",$snsf[0]),
    'minute','minutes' => sprintf("약%d 분전",$snsf[0]),
    'hour','hours'     => sprintf("약%d 시간전",$snsf[0]),
    'day','days'       => sprintf("약%d 일전",$snsf[0]),
    'month','months'   => sprintf("약%d 개월전",$snsf[0]),
    default            => $data->signdate
};

# output
return [
    "result" => 'true',
    "msg"    => $data->fetch()
];
?>