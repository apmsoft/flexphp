<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Request\Request;
use Flex\Annona\Request\FormValidation;
use Flex\Annona\Model;
use Flex\Annona\Date\DateTimez;

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
    (new FormValidation('name','이름',$request->name))->null()->disliking([]);
    (new FormValidation('email','이메일',$request->email))->null()->space()->email();
    (new FormValidation('extract_id','토큰',$request->extract_id))->null()->disliking([]);
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

# insert
$db->autocommit(FALSE);
try{
    $db['name']       = $request->name;
    $db['email']      = $request->email;
    $db['extract_id'] = $request->extract_id;
    $db['signdate']   = (new DateTimez("now"))->format('Y-m-d H:i:s');
    $db->table($tables->member)->insert();
}catch(\Exception $e){
    Log::e($e->getMessage());
}
$db->commit();

# output
return [
    "result" => 'true',
    "msg"    => $sysmsg->v_insert
];
?>