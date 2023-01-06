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
$request = (object)(new Request())->get()->fetch();

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

# Database
$db = new DbMySqli();

# 데이터 체크
$data = new Model(
    $db->table(R::tables('test'))->where('id',$request->id)->query()->fetch_assoc()
);
if(!isset($data->id)){
    return ["result"=>"false","msg_code"=>"e_db_unenabled","msg"=>R::sysmsg('e_db_unenabled')];
}

#r
$r = R::select(['array'=>"is_push,random_params"]);

# output
return [
    "result" => 'true',
    'r'      => $r,
    "msg"    => $data->fetch()
];
?>