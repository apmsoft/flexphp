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
$form->chkNumber('idxlen', '선택 퀄럼수', $req->idxlen, true);
$form->chkEngNumUnderline('idxlist', '인텍스퀄럼', str_replace(',','',$req->idxlist), true);

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

$scheme = array();
$scheme = array(
    'tname' => array(
        'type'=>'hidden',
        'value'=>$req->tname
    ),
    'idx_key_name' => array(
        'readonly' => (($req->idxlen>1) ? '' : 'readonly'),
        'title' => 'INDEX 키명',
        'value'=>(($req->idxlen>1) ? '' : $req->idxlist)
    ),
    'idx_list' => array(
        'readonly' => 'readonly',
        'title' => 'INDEX 퀄럼',
        'value'=>$req->idxlist
    ),
    'idx_len' => array(
        'type'=>'hidden',
        'value'=>$req->idxlen
    )
);

# output
out_json(array(
    'result' =>'true',
    'scheme' => $scheme,
	'msg'    =>$columns
));
?>
