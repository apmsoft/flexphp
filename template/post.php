<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Request\Request;
use Flex\Annona\Model;
use Flex\Annona\Token\TokenGenerateBtype;

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

# resource
R::tables();
R::array();
$sysmsg = R::dic(R::$sysmsg[R::$language]);
$array  = R::dic(R::$array[R::$language]);

# output
return [
    "result" => 'true',
    "r"      => $r,
    "msg"    => [
        'extract_id' => (new TokenGenerateAtype( null,10 ))->generateHashKey('md5')->value;
    ]
];
?>