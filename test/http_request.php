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

$urls[] = [
    'url' => 'https://www.google.com/search?q=flexup&newwindow=1&rlz=1C5CHFA_enKR1016KR1016&sxsrf=ALiCzsYQE1MJoEP_h05RgJ1sFR-21K_JeQ%3A1671104725091&ei=1QibY7OdBdLZ-QaQzYKAAg&ved=0ahUKEwjz25Wcxvv7AhXSbN4KHZCmACAQ4dUDCA8&uact=5&oq=flexup&gs_lcp=Cgxnd3Mtd2l6LXNlcnAQAzIICAAQgAQQywEyCAgAEIAEEMsBMgoIABCABBAKEMsBMgoIABCABBAKEMsBMggIABCABBDLATIKCAAQgAQQChDLATIICAAQgAQQywEyCggAEIAEEAoQywEyCAgAEIAEEMsBMg4ILhCABBDHARCvARDLAToKCAAQRxDWBBCwAzoECCMQJzoECAAQQzoNCC4QxwEQ0QMQ1AIQQzoHCCMQ6gIQJzoNCC4QxwEQ0QMQ6gIQJzoLCAAQgAQQsQMQgwE6BQgAEIAEOggIABCABBCxAzoNCAAQgAQQsQMQgwEQCkoECEEYAEoECEYYAFCeBljMIGCxI2gEcAF4AIABfYgBqAaSAQMwLjeYAQCgAQGwAQrIAQrAAQE&sclient=gws-wiz-serp',
    'params' => [
        // 'id' => 'test@dd.com',
        // 'msg'=> 'ok'
    ],
    'headers'=> [
        // 'Authorization-Access-Token:safdsaesfdsafdsa'
    ]
];
try{
    (new Flex\Http\HttpRequest( $urls ))->get(function($data) use ($urls){
        if(is_array($data)){
            foreach($data as $idx => $contents){
                Log::d('-------------------------');
                Log::d('-------------------------');
                Log::d('-------------------------',$urls[$idx]['url']);
                Log::d('-------------------------');
                Log::d('-------------------------');

                $htmlXssChars = new \Flex\Html\HtmlXssChars( $contents );
                Log::d($htmlXssChars->getContext('XSS'));
                Log::d('-------------------------');
                Log::d('-------------------------');
                Log::d('-------------------------');
                Log::d('-------------------------');
                Log::d('-------------------------');
            }
        }
        #return $data[0];
    });
    // (new Flex\Http\HttpRequest( $urls ))->post(function($data){
    //      Log::d($data);
    // });
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>