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

# Model
#$model = new UtilModel();
#out_r($model->fetch());

# resource
R::init(_LANG_);
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.'layout_adm.json', 'layout');

# check
if(!isset(R::$layout[$req->id])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.'layout_adm'.DIRECTORY_SEPARATOR.'config.php';

# 관리자 메니페스트 목록
R::parserResource(_ROOT_PATH_.'/'._RES_.'/manifest_adm.json', 'madm');

$data = array();
$scheme = array();
$scheme = array(
    'vkey' => array(
        'title' => '변수명',
        'readonly' => (in_array($req->id, $strings_filters)) ? 'readonly' : ''
    ),
    "filename" => array(
        "title" => "*파일경로",
        "comment" => "프로그램이 실행가능한 파일 경로를 입력하세요.",
        "value" => "coconut/program/program.html"
    ),
    "title" => array(
        "title" => "*타이틀",
        "comment" => "페이지 타이틀을 입력하세요"
    ),
    "category" => array(
        "type" => "select",
        "title" => "*카테고리",
        "default" => $menu_postion,
        "comment" => "페이지 분류 코드입니다.(drawermenu, program, develop)"
    ),
    "manifid" => array(
        "type" =>"select",
        "title" => "메니페스트",
        "comment" => "실행할 메니페스트를 선택하세요",
        "default" => convertArrayVal2KeyVV(array_keys(R::$r->madm))
    )
);
$data = array(
    'vkey' => $req->id,
    'filename' => R::$layout[$req->id]['filename'],
    'title' => R::$layout[$req->id]['title'],
    'category' => R::$layout[$req->id]['category'],
    'manifid' => R::$layout[$req->id]['manifid']
);

# output
out_json(array(
    'result' => 'true',
    'scheme' => $scheme,
    'msg' => $data
));
?>
