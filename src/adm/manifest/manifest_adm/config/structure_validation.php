<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Dir\DirObject;
use Fus3\Db\DbMySqli;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('manifid','메니페스트 ID', $req->manifid, true);
$form->chkEngNumUnderline('cfid','실행프로그램 ID', $req->cfid, true);

# model
$model = new UtilModel();
$model->find_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_;

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');
R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# config 있는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if(!$is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

$config_filename = R::$manifest[$req->manifid]['config'][$req->cfid];

# validation 클래스 함수 목록
$reflectionClass = new ReflectionClass('Fus3\req\ReqForm');
$methods = $reflectionClass->getMethods();
$chkfilters = array();
if(is_array($methods)){
    $methods_count = count($methods);
    // print_r($methods);
    for($mi =1; $mi < $methods_count; $mi++){
        $method_arg = (array)$methods[$mi];
        // out_r($method_arg);
        $method_name = $method_arg['name'];
        if($method_name != '__construct' && $method_name !='error_report'){
            $chkfilters[$method_name] = (isset($config_validation[$method_name])) ? $config_validation[$method_name] : '';
        //     $chkfilters[$method_name] = array();

        //     $parameters = $reflectionClass->getMethod($method_name)->getParameters();
        //     $parameters_count = count($parameters);
        //     for($pj =0; $pj < $parameters_count; $pj++){
        //         $parameters_arg = (array) $parameters[$pj];
        //         $chkfilters[$method_name][] = $parameters_arg['name'];
        //     }
        }
    }
}

# output
out_json(array(
    'result' =>'true',
    'chkfilters' => $chkfilters,
	'msg'    => array()
));
?>
