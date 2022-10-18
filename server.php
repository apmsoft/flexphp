<?php
use React\EventLoop\Loop;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Loop::get();

# DEFINE
define('_LOGFILE_','log.txt');
error_log (PHP_EOL.$_SERVER['REMOTE_ADDR'].PHP_EOL, 3, _LOGFILE_);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', function($params){
        parse_str($params, $url_queries);
        error_log ("/users -> params -> ".json_encode($url_queries).PHP_EOL, 3, _LOGFILE_);
        return ['result'=>'true', 'msg'=>'ok'];
    });

    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', function($params){
        parse_str($params, $url_queries);
        error_log ('<<< /user/id:+ >> params : '.json_encode($params).PHP_EOL, 3, _LOGFILE_);
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
            error_log ($uri.PHP_EOL, 3, _LOGFILE_);

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
        error_log ($router_path.PHP_EOL, 3, _LOGFILE_);
        
        return new Promise(function ($resolve) use ($dispatcher, $router_path,$params,$method)
        {
            $routeInfo = $dispatcher->dispatch($method, $router_path);
            switch ($routeInfo[0])
            {
                case FastRoute\Dispatcher::NOT_FOUND:
                    error_log ("404 Not Found".PHP_EOL, 3, _LOGFILE_);
                    break;
                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    error_log ($allowedMethods." >> 405 Method Not Allowed".PHP_EOL, 3, _LOGFILE_);
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
    error_log ($message.PHP_EOL, 3, _LOGFILE_);
    header('Content-Type: application/json; charset=utf-8');
    echo $message;
}, function (Exception $e) {
    echo '// ['.__LINE__.']'.$e->getMessage().' //'.PHP_EOL;
});

$loop->run();
?>