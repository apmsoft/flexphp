<?php
use React\EventLoop\Loop;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

use Flex\R\R;
use Flex\Log\Log;

$path = __DIR__;
require $path. '/vendor/autoload.php';
require $path. '/config/config.inc.php';

$loop = React\EventLoop\Loop::get();

# DEFINE
define('_LOGFILE_','log.txt');

# Log setting
Log::init();
Log::i($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', function($params){
        parse_str($params, $url_queries);
        Log::d("/users -> params -> ".json_encode($url_queries));
        return ['result'=>'true', 'msg'=>'ok'];
    });

    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', function($params){
        parse_str($params, $url_queries);
        Log::d('<<< /user/id:+ >> params : '.json_encode($params));

        return ['result'=>'true','msg'=>'id'];
    });
});


React\Async\waterfall(
[
    # 접속정보
    function ($params =[])
    {
        return new Promise(function ($resolve)
        {
            // Fetch method and URI from somewhere
            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];
            Log::d($uri);

            $url_parse = parse_url($uri);

            # resolve
            $resolve($url_parse);
        });
    },

    # Router
    function ($params) use ($dispatcher)
    {
        $path   = $params['path'];
        $params = $params['query'];
        $method = $_SERVER['REQUEST_METHOD'];
        $router_path= strtr($path,['/index.php'=>'']);

        Log::d($router_path);
        
        return new Promise(function ($resolve) use ($dispatcher, $router_path,$params,$method)
        {
            $routeInfo = $dispatcher->dispatch($method, $router_path);
            switch ($routeInfo[0])
            {
                case FastRoute\Dispatcher::NOT_FOUND:
                    Log::e("404 Not Found");
                    break;
                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    Log::e($allowedMethods." >> 405 Method Not Allowed");
                    break;
                case FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];

                    $data = json_encode($handler($params));
                    $resolve($data);
                    break;
            }
        });
    }
])
->then(function ($message){
    Log::v($message);
    header('Content-Type: application/json; charset=utf-8');
    echo $message;
}, function (Exception $e) {
    echo '// ['.__LINE__.']'.$e->getMessage().' //'.PHP_EOL;
});

$loop->run();
?>