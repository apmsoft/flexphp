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
$form->chkEngNumUnderline('idx_key_name', 'INDEX 키명', $req->idx_key_name, true);

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

# 이미 인텍스 key_name 이 있는지 체크
# 테이블에 해당하는 index 정보
$table_index = array();
$table_index_qry = sprintf("show index from `%s`",$model->table);
if($table_index_rlt = $db->query($table_index_qry)){
    while($table_index_row = $table_index_rlt->fetch_assoc()){
        $table_index[] = $table_index_row['Key_name'];
    }
}

if(array_search($req->idx_key_name, $table_index)>-1){
} else {
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 테이블 복사 및 새로 만들기
$dropindex_qry = sprintf("ALTER table `%s` DROP INDEX %s", $model->table, $req->idx_key_name);
if(!$db->query($dropindex_qry)){
    out_json(array('result'=>'false','msg_code'=>'e_drop_index_column','msg'=>'Error Drop Index Column'));
}

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_delete']
));
?>
