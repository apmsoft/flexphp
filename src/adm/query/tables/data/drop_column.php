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
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('tname', '테이블명', $req->tname, true);
$form->chkEngNumUnderline('column_name', '퀄럼 명', $req->column_name, true);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명

# check key
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# db
$db = new DbMySqli();

# 실제 테이블이 있는지 체크
$realtable_qry = sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s' LIMIT 1", $model->table);
if(!$realtable_rlt = $db->query($realtable_qry)){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 퀄럼 지우기
$dropcolumn_qry = sprintf("ALTER TABLE `%s` DROP COLUMN `%s`", $model->table, $req->column_name);
$db->query($dropcolumn_qry);

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_delete']
));
?>
