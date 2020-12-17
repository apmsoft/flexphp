<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ftp.php';

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
$form->chkEngNumUnderline('id', 'id', $req->id, true);

# resource
R::init(_LANG_);
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# check
if(!isset(R::$r->array[$req->id])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'values'.DIRECTORY_SEPARATOR.'array'.DIRECTORY_SEPARATOR.'config.php';

$data = array();
$scheme = array();
$scheme = array(
    'vkey' => array(
        'title' => '변수명',
        'readonly' => (in_array($req->id, $strings_filters)) ? 'readonly' : ''
    ),
    'vval' => array(
        'type' => 'textarea',
        'title' => '변수값'
    )
);
$data = array(
    'vkey' => $req->id,
    'vval' => strval(json_encode(R::$r->array[$req->id], JSON_UNESCAPED_UNICODE))
);

# output
out_json(array(
    'result' => 'true',
    'scheme' => $scheme,
    'msg' => $data
));
?>
