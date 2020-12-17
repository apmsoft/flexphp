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
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('tname', '테이블명', $req->tname, true);
$form->chkNull('id', '수정할데이터', $req->id, false);

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
$model->columns = 'COLUMN_NAME,ORDINAL_POSITION,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,COLUMN_KEY,EXTRA,COLUMN_COMMENT';

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'query'.DIRECTORY_SEPARATOR.'tables'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'config.php';

# db
$db = new DbMySqli();

# 테이블 정보
$table_info = array();
$table_info_qry = sprintf("SELECT TABLE_NAME,TABLE_ROWS,ENGINE,UPDATE_TIME,TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME ='%s'",$model->table);
if($table_info_rlt = $db->query($table_info_qry)){
    $table_info = $table_info_rlt->fetch_assoc();
    if(!isset($table_info['TABLE_NAME'])){
        out_json(array('result'=>'false','msg_code'=>'e_usernotfound','msg'=>R::$sysmsg['e_usernotfound']));
    }
}

# data
$data_info = $db->get_record('*', $model->table, sprintf("`id`='%u'", $req->id));
if(!isset($data_info['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 퀄럼 정보
$columns = array();
$qry = sprintf("SELECT %s FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->columns,$model->table);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc())
{
    $_this_column_name = $row['COLUMN_NAME'];
    if($model->primary_key ==''){
		if($row['COLUMN_KEY'] == 'PRI'){
            $model->primary_key = $_this_column_name;
		}
    }
    $column_name = $_this_column_name;
    $auto_increment = '';
    $index = '';
    $index_key_name = '';
    $column_extra = '';
    
    # EXTRA
	if(isset($row['EXTRA']) && $row['EXTRA']){
        if($row['EXTRA'] == 'auto_increment'){
            $auto_increment = 'YES';
        }
    }

	$columns[] = array(
		'column_name'     => $_this_column_name,
        'auto_increment'  => $auto_increment,
		'data_type'       => $row['COLUMN_TYPE'],
		'is_null'         => $row['IS_NULLABLE'],
		'default'         => $row['COLUMN_DEFAULT'],
        'comment'         => $row['COLUMN_COMMENT'],
        'val'             => $data_info[$_this_column_name]
	);
}

# output
out_json( array(
    'result'      => 'true',
    'primary_key' => $model->primary_key,
    'msg'         => $columns
));
?>
