<?php
# item_0338865001668157100
# 2de2d16e8894925aa76727bb1024b07a.jpg
?><?php
use React\EventLoop\Loop;
use React\Http\Server;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Promise\Promise;

use Flex\Annona\R;
use Flex\Annona\App;
use Flex\Annona;

# config
$path = __DIR__;
require $path .'/vendor/autoload.php';
require $path.'/config/config.inc.php';

// error_reporting(E_ALL);
error_reporting(E_ERROR | ~E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 'On');

# Log setting
# 메세지출력 방법 : echo [Flex\Annona\Log::MESSAGE_ECHO], file [Flex\Annona\Log::MESSAGE_FILE]
# default 값: Flex\Annona\Log::MESSAGE_FILE, filename : log.txt
define ('_LOG_',sprintf("%s/%s/log_item_%s.txt",_ROOT_PATH_,_DATA_,date('ymd')));
if(!file_exists(_LOG_)){
    $preferenceInternalStorage = new \Flex\Annona\Preference\PreferenceInternalStorage(_LOG_,'w');
    $preferenceInternalStorage->writeInternalStorage('start'.PHP_EOL);
}
Flex\Annona\Log::init(Flex\Annona\Log::MESSAGE_FILE,_LOG_);
Flex\Annona\Log::setDebugs('i','d','v','e');
Flex\Annona\Log::options([
    'datetime'   => true,
    'debug_type' => true,
    'newline'    => true
]);

$loop = React\EventLoop\Loop::get();

# brower
$browser = new React\Http\Browser();

# classes
$req = new \Flex\Annona\Req\Req();

# resources
R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/imageviewer.json', 'config');

# dispather
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($req)
{
    $r->addRoute('GET', '/imageview/{e:\w+}/{n}', function($path){
        $_uri_queies = explode('/',$path);

        $extract_id = $_uri_queies[2];

        # 기본정보
        $filename    = $_uri_queies[3];     # 파일명
        $compression = 100;    # 품질(압축)
        $sizes       = '';    # 이미지사이즈

        # 압축 및 이미지사이즈 체크
        if(strpos($_uri_queies[3],'@') !==false) 
        {
            $params = explode('@',$_uri_queies[3]);
            parse_str($params[1], $parse_str);

            $filename = $params[0];
            if(isset($parse_str['c'])){ $compression = R::$config[$parse_str['c']]; }
            if(isset($parse_str['s'])){ $sizes = R::$config[$parse_str['s']]; }
        }

        Flex\Annona\Log::d('filename : '.   $filename);
        Flex\Annona\Log::d('compression : '.$compression);
        Flex\Annona\Log::d('sizes : '.$sizes);
        
        $imageViewer = new \Flex\Annona\Image\ImageViewer( $extract_id );
        return $imageViewer->doView( $filename, $compression, $sizes );
    });
});

# promise
$deferred = new \React\Promise\Deferred();
$deferred->promise()
    # 접속정보
    ->then(function ($ok)
    {
        # 기본정보
        $uri = $_SERVER['REQUEST_URI'];
        Flex\Annona\Log::i($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_METHOD'], $uri);

        # url parsing
        $url_parse = parse_url($uri);

        # resolve
        return $url_parse;
    })
    # query
    ->then(function ($params) use ($dispatcher, $deferred)
    { 
        $path   = $params['path'];
        $params = $params['query'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];
        $router_path= strtr($path,['/imageviewer.php'=>'']);

        Flex\Annona\Log::d('path : '.$router_path, 'params : '.$params);

        $routeInfo = $dispatcher->dispatch($method, $router_path);
        switch ($routeInfo[0])
        {
            case FastRoute\Dispatcher::NOT_FOUND:
                Flex\Annona\Log::e("404 Not Found");
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                Flex\Annona\Log::e($allowedMethods." >> 405 Method Not Allowed");
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];

                $file = $handler($router_path);
                Flex\Annona\Log::v(json_encode($file));

                header(sprintf("Content-type:%s",$file['mimeType']));
                // header("Cache-control:private");
                // header(sprintf("Content-Disposition:attachment;filename=\"%s\"", $file['filename']));
                // header("Content-Transfer-Encoding:binary");
                // header("Pragma:no-cache");

                echo $file['contents'];
                break;
        }
    })
    ->otherwise(function ($e){
        Flex\Annona\Log::e($e->getMessage());
    });

$deferred->resolve(1);

$loop->run();
?>