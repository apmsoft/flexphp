<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';
#require $path. '/vendor/autoload.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


try{
    (new Flex\Annona\Http\HttpRequest())
    ->set(
        'http://115.68.73.34:8000/remaining/amount',
        http_build_query([]),
        [
            "Content-Type:application/x-www-form-urlencoded",
            "Accept-Language:ko",
            "Authorization-Access-Token:Y29taGhzZWVnMzpGWDRqcklsZGhxMjk0ZThnV0NoTDJ3"
        ]
    )
    ->post(function($data){
        print_r($data);
    });
    // (new Flex\Annona\Http\HttpRequest( $urls ))->post(function($data){
    //      Log::d($data);
    // });
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>