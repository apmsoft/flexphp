<?php
use React\EventLoop\Loop;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

$path = __DIR__;
require $path. '/vendor/autoload.php';
require $path. '/config/config.inc.php';

$loop = React\EventLoop\Loop::get();

# Log setting
# 메세지출력 방법 : echo [Log::MESSAGE_ECHO], file [Log::MESSAGE_FILE]
# default 값: Log::MESSAGE_FILE, filename : log.txt
Log::init(Log::MESSAGE_ECHO);
Log::setDebugs('i','d','v','e');
// Log::options([
//     'datetime'   => true,
//     'debug_type' => true,
//     'newline'    => true
// ]);

# Routers
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/favicon.ico', function(ServerRequestInterface $request){
        return ['result'=>'true', 'msg'=>'Hello'];
    });
    $r->addRoute('GET', '/', function(ServerRequestInterface $request){
        return ['result'=>'true', 'msg'=>'Hello'];
    });
    
    $r->addRoute('GET', '/users', function(ServerRequestInterface $request){
        return ['result'=>'true', 'msg'=>'ok'];
    });

    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', function(ServerRequestInterface $request){
        return ['result'=>'true','msg'=>'id'];
    });

    $r->addRoute('GET', '/download', function(ServerRequestInterface $request){
        $file_contents = (new \Flex\Annona\File\Download( _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe/thumb90100x100_j.jpeg' ))->getContents();
        return ['result'=>'true','msg'=>$file_contents];
    });
});

$http = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) use ($dispatcher)
{
    # 기본정보
    $client_ip = $request->getServerParams()['REMOTE_ADDR'];
    $uri_path  = $request->getUri()->getPath();
    $method    = $request->getMethod();
    Log::i($client_ip, $method, $uri_path);

    return React\Async\waterfall(
    [    
        # Router
        function ($params=[]) use ($dispatcher,$request,$method,$uri_path)
        {            
            return new Promise(function ($resolve) use ($dispatcher, $request, $method, $uri_path)
            {
                # headers
                $headers_all = (new \Flex\Annona\Request\Request)->getHeaders();
                Log::d($headers_all);
                $routeInfo = $dispatcher->dispatch($method, $uri_path);
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
    
                        $data = $handler($request);
                        $resolve($data);
                        break;
                }
            });
        }
    ])
    ->then(function ($message) use ($uri_path){
        Log::v($message);

        // if($uri_path=='/download'){
        //     return new React\Http\Message\Response(React\Http\Message\Response::STATUS_OK, [
        //         'Content-Type'              => 'application/octet-stream',
        //         'Cache-control'             => 'privat  e',
        //         'Content-Disposition'       => sprintf("attachment;filename=\"%s\"", '테스트.jpeg'),
        //         'Content-Transfer-Encoding' => 'binary',
        //         'Pragma'                    => 'no-cache'
        //     ], $message['msg']);
        // }else{
            return new React\Http\Message\Response(React\Http\Message\Response::STATUS_OK, [
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin'=>'*',
                'Access-Control-Allow-Headers'=>'*',
                'Access-Control-Allow-Methods'=>'*'
            ],json_encode($message));
        // }

    }, function (Exception $e) {
        echo $e;
        echo '// ['.__LINE__.']'.$e->getMessage().' //'.PHP_EOL;
    });
});

$socket = new React\Socket\SocketServer('0.0.0.0:80');
Log::d('Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()));
$http->listen($socket);

// $loop->run();
?>