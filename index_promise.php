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

# Log setting
# 메세지출력 방법 : echo [Log::MESSAGE_ECHO], file [Log::MESSAGE_FILE]
# default 값: Log::MESSAGE_FILE, filename : log.txt
Log::init();
Log::setDebugs('i','d','v','e');
Log::options([
    'datetime'   => true,
    'debug_type' => true,
    'newline'    => true
]);

# Routers
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

# promise
$deferred = new \React\Promise\Deferred();
$deferred->promise()
    # 접속정보
    ->then(function ($module_id)
    {
        # 기본정보
        $uri = $_SERVER['REQUEST_URI'];
        Log::i($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_METHOD'], $uri);

        # url parsing
        $url_parse = parse_url($uri);

        # resolve
        return $url_parse;
    })
    # query
    ->then(function ($params) use ($dispatcher, $deferred)
    { 
        $path   = $params['path'];
        $params = $params['query'];
        $method = $_SERVER['REQUEST_METHOD'];
        $router_path= strtr($path,['/index.php'=>'']);

        Log::d($router_path);

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
                Log::v($data);
                header('Content-Type: application/json; charset=utf-8');
                echo $message;
                break;
        }
    })
    ->otherwise(function ($e){
        Log::e($e->getMessage());
    });

$deferred->resolve($client_ip);

$loop->run();
?>