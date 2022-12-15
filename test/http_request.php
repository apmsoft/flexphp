<?php
# session_start();
use Flex\App\App;
use Flex\Log\Log;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';
#require $path. '/vendor/autoload.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


$urls = [];
$urls[] = [
    'url' => 'https://search.naver.com/search.naver?display=15&f=&filetype=0&page=2&query=flexup&research_url=&sm=tab_pge&start=1&where=web',
    'params' => [
        // 'id' => 'test@dd.com',
        // 'msg'=> 'ok'
    ],
    'headers'=> [
        // 'Authorization-Access-Token:safdsaesfdsafdsa'
    ]
];
try{
    $httpRequest = new Flex\Http\HttpRequest();
    $httpRequest($urls)->get(function($data){
        Log::d($data);
        #return $data[0];
    });
    // $httpRequest($urls)->post(function($data){
    //      Log::d($data);
    //      return $data[0];
    // });
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>