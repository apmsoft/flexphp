<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Db\DbHelperWhere;
use Fus3\Paging\PagingRelation;

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
$form->chkNull('dd', '삭제할데이터', $req->dd, false);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# check
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명
$model->primary_key = '';

# db
$db = new DbMySqli();

$rlt = $db->query(sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->table));
while($row = $rlt->fetch_assoc())
{
	if($model->primary_key ==''){
		if($row['COLUMN_KEY'] == 'PRI'){
            $model->primary_key = $row['COLUMN_NAME'];
            break;
		}
    }
}

# dd
$_dd = array();
if(strpos($req->dd,',') !==false){
    $_dd = explode(',', $req->dd);
}else{
    $_dd[] = $req->dd;
}

if(is_array($_dd)){
    foreach($_dd as $num => $data_id){
        $_wh = sprintf("`%s`='%s'", $model->primary_key, $data_id);
        $info = $db->get_record($model->primary_key, $model->table, $_wh);
        if(isset($info[$model->primary_key])){
            $db->delete($model->table, $_wh);
        }
    }
}

# output
out_json( array(
    'result' => 'true',
    'msg'    => R::$sysmsg['v_delete']
));
?>
