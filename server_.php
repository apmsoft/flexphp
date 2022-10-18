<?php
use React\EventLoop\Loop;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Loop::get();

#$source = new React\Stream\ReadableResourceStream(fopen('log.txt', 'r',10));
// $dest = new React\Stream\WritableResourceStream(fopen('log.txt', 'a'));
// $dest->write(PHP_EOL.$_SERVER['REMOTE_ADDR'].PHP_EOL);
define('_LOGFILE_','log.txt');
error_log (PHP_EOL.$_SERVER['REMOTE_ADDR'].PHP_EOL, 3, _LOGFILE_);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($dest) {
    $r->addRoute('GET', '/users', function($params) use ($dest){
        parse_str($params, $url_queries);
        // $dest->write("/users -> params -> ".json_encode($url_queries).PHP_EOL );
        error_log ("/users -> params -> ".json_encode($url_queries).PHP_EOL, 3, _LOGFILE_);
        return ['result'=>'true', 'msg'=>'ok'];
    });

    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', function($params) use ($dest){
        parse_str($params, $url_queries);
        // $dest->write('<<< /user/id:+ >> params : '.json_encode($params).PHP_EOL );
        error_log ('<<< /user/id:+ >> params : '.json_encode($params).PHP_EOL, 3, _LOGFILE_);
        return ['result'=>'true','msg'=>'id'];
    });
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
// $dest->write($uri.PHP_EOL);
error_log ($uri.PHP_EOL, 3, _LOGFILE_);

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$uri_handler = strtr($uri,['/index.php'=>'']);
// $dest->write( $uri_handler.PHP_EOL );
error_log ($uri_handler.PHP_EOL, 3, _LOGFILE_);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri_handler);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // $dest->write("404 Not Found".PHP_EOL);
        error_log ("404 Not Found".PHP_EOL, 3, _LOGFILE_);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // $dest->write($allowedMethods." >> 405 Method Not Allowed".PHP_EOL);
        error_log ($allowedMethods." >> 405 Method Not Allowed".PHP_EOL, 3, _LOGFILE_);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

        echo $data = json_encode($handler($vars));
        error_log ($data.PHP_EOL, 3, _LOGFILE_);
        $dest->write($data.PHP_EOL);
        return new Response(200, ['Content-Type' => 'application/json'], $data);
        #return new Response(200, ['Content-Type' => 'text/html'], $data);
        break;
}
$loop->run();