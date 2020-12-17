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
$form->chkNumber('idx_len', '선택 퀄럼수', $req->idx_len, true);
$form->chkEngNumUnderline('idx_list', '인텍스퀄럼', str_replace(',','',$req->idx_list), true);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명
$model->add_index_params_str = '';
$columns = array();

# check key
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# db
$db = new DbMySqli();

# 실제 테이블이 있는지 체크
$realtable_qry = sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->table);
$realtable_rlt = $db->query($realtable_qry);
if(!$realtable_rlt){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

while($realtable_info = $realtable_rlt->fetch_assoc()){
    $columns[] = $realtable_info['COLUMN_NAME'];
}

# 같은 퀄럼이 있는지 체크
$chk_idx_columns = ($req->idx_len>1) ? explode(',',$req->idx_list) : array($req->idx_list);
$model->add_index_params_str ='(';
foreach($chk_idx_columns as $n => $column_nm)
{
    if(array_search($column_nm, $columns) > -1){
        $model->add_index_params_str .= ($n > 0) ? sprintf(",`%s`", $column_nm) : sprintf("`%s`", $column_nm);
    }else{
        out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>'[3]'.R::$sysmsg['e_db_unenabled']));
    }
}
$model->add_index_params_str.=')';

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
    out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
}

# 퀄럼 정보 변경
#ALTER table `books` DROP INDEX theindex;
#ALTER table `books` ADD INDEX theindex (`date`, `time`);
$addindex_qry = sprintf("ALTER table `%s` ADD INDEX %s %s", 
    $model->table,
        $req->idx_key_name,
            $model->add_index_params_str
        );
if(!$rlt = $db->query($addindex_qry)){
    out_json(array('result'=>'false','msg_code'=>'e_add_index_column','msg'=>'Error Add Index Column'));
}

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_insert']
));
?>
