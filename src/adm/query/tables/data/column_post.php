<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;

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
$form->chkEngNumUnderline('tname', '테이블명', $req->tname, true);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# check
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'query'.DIRECTORY_SEPARATOR.'tables'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'config.php';

# model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명

# db
$db = new DbMySqli();

$columns = array();
$columns = array(
    'tname' => $req->tname,
    'column_name'    => '',
    'data_type'      => '',
    'data_length'    => '',
    'is_null'        => '',
	'data_unsigned'  => '',
    // 'data_primary'   => ($primary_key) ? 'PRI' : '',
    // 'auto_increment' => $auto_increment,
	
	'data_default'   => '',
	'comment'        => ''
);

$scheme = array();
$scheme = array(
    'tname' => array(
        'type'=>'hidden'
    ),
    'column_name' => array(
        'title' => '*퀄럼명'
    ),
    'data_type' => array(
        'title' => '*데이터 타입',
        'type' => 'select',
        'default' => $column_data_types
    ),
    'data_length' => array(
        'title' => '*LENGTH',
        'type' => 'integer',
        'comment' =>'DataType 이 ENUM,DECIMAL 일경우 쉼표을 기준으로 길이(크기)를 입력하세요'
    ),
    'is_null' => array(
        'title' => '*NULL',
        'type' => 'radio',
        'default' => $column_data_null
    ),
    'data_unsigned' => array(
        'title' => 'UNSIGNED',
        'type' => 'checkbox',
        'readonly' => ($disabled) ? 'disabled':'',
        'default' => array('UNSIGNED'=>'UNSIGNED'),
        'comment' =>'DataType 이 숫자일 경우에만 적용됩니다(0~).'
    ),
    // 'data_primary' => array(
    //     'title'=> 'PRIMARY KEY',
    //     'type' => 'checkbox',
    //     'default' => array('PRI'=>'PRI')
    // ),
    // 'auto_increment' => array(
    //     'title'=> 'AUTO_INCREMENT',
    //     'type' => 'checkbox',
    //     'default' => array('auto_increment'=>'auto_increment')
    // ),
    'data_default' => array(
        'title' => 'DEFAULT',
        'comment'=>'DataType 이 숫자 또는 ENUM 일경우 기본 값이 필요합니다.'
    ),
    'comment' => array(
        'title' => '*COMMENT',
        'comment'=>'퀄럼 타이틀을 입력하세요'
    )
);

# output
out_json(array(
    'result' =>'true',
    'scheme'  => $scheme,
	'msg'    =>$columns
));
?>
