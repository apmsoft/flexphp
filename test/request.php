<?php
use Flex\App\App;
use Flex\Log\Log;

use Flex\R\R;
use Flex\Request\Request;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# GET
$get_data = (new Request())->get()->fetch();
Log::d('GET',$get_data);

# POST
$post_data = (new Request())->post()->fetch();
Log::d('GET',$post_data);

# 
$request = new Request();
$request->a = 'a';
$request->b = 'b';
$request->c = 'c';
Log::d('GET',$request->fetch());
?>