<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
// use Fus3\Req\ReqForm;

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
// $form = new ReqForm();
// $form->chkEngNumUnderline('id', 'id', $req->id, true);

# Model
#$model = new UtilModel();
#out_r($model->fetch());

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');
R::parserResourceDefinedID('tables');
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

$uploadable_form = array(
    "table"        => "",
    "authority"    => 0,
    "image_size"   => array(
        "thumbnail"=> array("width" => 110,"height" => 110),
        "middle"   => array("width" => 600,"height" => 600)
    ),
    "file_extension"=> "jpg,jpeg,png",
    "file_maxsize" => 8,
    "number_files_allowed" => 1
);

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config.php';

$manifest_data =&R::$manifest[$req->id];

$data = array();
$scheme = array();
$scheme = array(
    'vkey' => array(
        'title' => '변수명',
        'readonly' => (in_array($req->id, $strings_filters)) ? 'readonly' : ''
    ),
    'category' => array(
        'title' => '카테고리'
    ),
    'title' => array(
        'title' => '타이틀'
    ),
    'config' => array(
        "type" => "config",
        'title' => '실행파일',
        'default' => array()
    ),
    'uploadable' => array(
        "type" => "uploadable",
        'title' => '첨부파일',
        'default' => $uploadable_form
    )
);

$data = array(
    'vkey' => $req->id,
    'category' => $manifest_data['category'],
    'title' => $manifest_data['title'],
    'config' => (isset($manifest_data['config']) ? 'true' : 'false'),
    'uploadable' => (isset($manifest_data['uploadable']) ? 'true' : 'false')
);

# output
out_json(array(
    'result' => 'true',
    'scheme' => $scheme,
    'tables' => R::$tables,
    'level' => R::$r->array['level'],
    'msg' => $data
));
?>
