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


// try{
//     (new Flex\Annona\Http\HttpRequest())
//     ->set(
//         'http://localhost:8000/remaining/amount',
//         http_build_query([]),
//         [
//             "Content-Type:application/x-www-form-urlencoded",
//             "Accept-Language:ko",
//             "Authorization-Access-Token:dfsafdsafdsasd"
//         ]
//     )
//     ->post(function($data){
//         print_r($data);
//     });
//     // (new Flex\Annona\Http\HttpRequest( $urls ))->post(function($data){
//     //      Log::d($data);
//     // });
// }catch(\Exception $e){
//     Log::e($e->getMessage());
// }


try{
    (new Flex\Annona\Http\HttpRequest())
    ->set(
        'http://192.168.50.165:8104/read',
        http_build_query([
            "name" => "홍길동",
            "uploadfile" => "@/app/test/ddd.png"
        ]),
        [
            "Content-Type:multipart/form-data"
        ]
    )
    ->post(function($data){
        print_r($data);
    });
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>