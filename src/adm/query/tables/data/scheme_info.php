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

# 테이블에 해당하는 index 정보
$table_index = array();
$table_index_qry = sprintf("show index from `%s`",$model->table);
if($table_index_rlt = $db->query($table_index_qry)){
    while($table_index_row = $table_index_rlt->fetch_assoc()){
        $col_name = $table_index_row['Column_name'];
        $table_index[$col_name] = array(
            'key_name' => $table_index_row['Key_name'],
            'non_unique' => $table_index_row['Non_unique'],
            'seq_in_index' => $table_index_row['Seq_in_index']
        );
    }
}

# 퀄럼 정보
$columns = array();
$qry = sprintf("SELECT %s FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->columns,$model->table);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc())
{
    if($model->primary_key ==''){
		if($row['COLUMN_KEY'] == 'PRI'){
            $model->primary_key = $row['COLUMN_NAME'];
		}
    }
    $column_name = $row['COLUMN_NAME'];
    $auto_increment = '';
    $index = '';
    $index_key_name = '';
    $column_extra = '';

	# INDEX
	// if(isset($row['COLUMN_KEY']) && $row['COLUMN_KEY']){
	// 	if($row['COLUMN_KEY'] == 'MUL'){
    //         $index = 'INDEX';
    //     }
    // }
    if(isset($table_index[$column_name])){
        $index_data =&$table_index[$column_name];
        if( $index_data['key_name'] == 'PRIMARY' ){
            $index = "UNIQUE";
            $index_key_name = 'PRIMARY';
        }else if( ($index_data['key_name'] != $column_name) && ($index_data['non_unique'] ==0) ){
            $index = sprintf("UNIQUE (%s) : %d", $index_data['key_name'],$index_data['seq_in_index']);
            $index_key_name = $index_data['key_name'];
        }else if( ($index_data['key_name'] != $column_name) && ($index_data['non_unique'] ==1) ){
            $index = sprintf("INDEX (%s) : %d", $index_data['key_name'],$index_data['seq_in_index']);
            $index_key_name = $index_data['key_name'];
        }else {
            $index = 'INDEX';
            $index_key_name = $index_data['key_name'];
        }
    }
    
    # EXTRA
	if(isset($row['EXTRA']) && $row['EXTRA']){
        if($row['EXTRA'] == 'auto_increment'){
            $auto_increment = 'YES';
        }else{
            $column_extra = $row['EXTRA'];
        }
	}

	$columns[] = array(
		'column_name'    => $row['COLUMN_NAME'],
        'column_extra'   => $column_extra,
        'auto_increment' => $auto_increment,
        'index'          => $index,
        'index_key_name' => $index_key_name,
		'data_type'      => $row['COLUMN_TYPE'],
		'is_null'        => $row['IS_NULLABLE'],
		'default'        => $row['COLUMN_DEFAULT'],
        'comment'        => $row['COLUMN_COMMENT'],
        'ordinal_postion'=> (int)$row['ORDINAL_POSITION']
	);
}

# output
out_json(array(
    'result'          => 'true',
    'strings_filters' => $strings_filters,
    'primary_key'     => $model->primary_key,
    'table_info'      => $table_info,
    'tname'           => $req->tname,
	'msg'             => $columns
));
?>
