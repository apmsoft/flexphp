<?php
use Fus3\Util\UtilController;
use Fus3\Req\Req;
use Fus3\Auth\AuthSession;
use Fus3\R\R;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션시작
$auth = new AuthSession($app['auth']);
$auth->sessionStart();

# req
$req = new Req;
$req->usePOST();

try{
    $controller = new UtilController($req->fetch());
    $controller->on('config');
    $controller->run('www');

    out_json($controller->output);
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>