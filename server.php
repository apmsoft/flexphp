<?php
use React\EventLoop\Loop;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\ChildProcess\Process;

require __DIR__ . '/vendor/autoload.php';

$loop = React\EventLoop\Loop::get();

$command = "echo %s >> log.txt";

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($command) {
    $r->addRoute('GET', '/users', function($params) use ($command){
        $process = new React\ChildProcess\Process(sprintf($command,"/users -> params -> ".$params ));
        $process->start($loop);
        return ['result'=>'true', 'msg'=>'ok'];
    });
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', function($params){
        $process = new React\ChildProcess\Process(sprintf($command,'<<< /user/id:+ >> params : '.$params ));
        $process->start($loop);
        return ['result'=>'true','msg'=>'id'];
    });
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$process = new React\ChildProcess\Process(sprintf($command,$uri));
$process->start($loop);

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$process = new React\ChildProcess\Process(sprintf($command,'>>> 2 '.strtr($uri,['/index.php'=>'']) ));
$process->start($loop);

$routeInfo = $dispatcher->dispatch($httpMethod, strtr($uri,['/index.php'=>'']));
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $process = new React\ChildProcess\Process(sprintf($command,"404 Not Found" ));
        $process->start($loop);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $process = new React\ChildProcess\Process(sprintf($command,"405 Method Not Allowed" ));
        $process->start($loop);
        break;
    case FastRoute\Dispatcher::FOUND:
        echo $data = json_encode($routeInfo[1]($vars));
        break;
}
$loop->run();
?>