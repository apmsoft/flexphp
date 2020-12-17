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
$form->chkEngNumUnderline('column_name', '퀄럼명', $req->column_name, true);

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
$model->primary_key = '';
$model->columns = 'COLUMN_NAME,DATA_TYPE,NUMERIC_PRECISION,NUMERIC_SCALE,CHARACTER_MAXIMUM_LENGTH,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT,COLUMN_KEY,EXTRA,COLUMN_COMMENT';

# db
$db = new DbMySqli();

$scheme = array();

$columns = array();
$qry = sprintf("SELECT %s FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s' and COLUMN_NAME='%s'", 
    $model->columns,
        $model->table,
            $req->column_name
    );
$rlt = $db->query($qry);
$row = $rlt->fetch_assoc();
if(!isset($row['COLUMN_NAME'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 데이터 타입체크
$disabled = true;
switch($row['DATA_TYPE']){
	case 'int':
	case 'mediumint':
	case 'smallint':
	case 'tinyint':
		$data_length = $row['NUMERIC_PRECISION'];
		if(strpos($row['COLUMN_TYPE'], 'unsigned') !==false){
			$data_unsigned = 'UNSIGNED';
        }
        
        $disabled = false;
	break;
	case 'enum':
		$data_length = strtr($row['COLUMN_TYPE'],array('enum'=>'','('=>'',')'=>'',"'"=>''));
	break;
	case 'decimal':
		$data_length = sprintf("%d,%d", $row['NUMERIC_PRECISION'], $row['NUMERIC_SCALE']);
	break;
	default :
		$data_length = $row['CHARACTER_MAXIMUM_LENGTH'];
}

$auto_increment = '';
$index = '';
$column_extra = '';
$primary_key = '';

# primary
if($row['COLUMN_KEY'] == 'PRI'){
    $primary_key = $row['COLUMN_NAME'];
}

# index
if(isset($row['COLUMN_KEY']) && $row['COLUMN_KEY']){
    if($row['COLUMN_KEY'] == 'MUL'){
        $index = 'INDEX';
    }
}

# EXTRA
if(isset($row['EXTRA']) && $row['EXTRA']){
    if($row['EXTRA'] == 'auto_increment'){
        $auto_increment = 'YES';
    }
}

$columns = array(
    'tname' => $req->tname,
    'column_name'    => $row['COLUMN_NAME'],
    'chg_column_name'=> $row['COLUMN_NAME'],
    'data_type'      => strtoupper($row['DATA_TYPE']),
    'data_length'    => $data_length,
    'is_null'        => ($row['IS_NULLABLE'] == 'NO') ? 'NOT NULL': 'NULL',
	'data_unsigned'  => $data_unsigned,
    // 'data_primary'   => ($primary_key) ? 'PRI' : '',
    // 'auto_increment' => $auto_increment,
	
	'data_default'   => $row['COLUMN_DEFAULT'],
	'comment'        => $row['COLUMN_COMMENT']
);

$scheme = array(
    'tname' => array(
        'type'=>'hidden'
    ),
    'column_name' => array(
        'title' => '퀄럼명',
        'readonly' => 'readonly'
    ),
    'chg_column_name' => array(
        'title' => '새 퀄럼명'
    ),
    'data_type' => array(
        'title' => '데이터 타입',
        'type' => 'select',
        'default' => $column_data_types
    ),
    'data_length' => array(
        'title' => 'LENGTH',
        'type' => 'integer',
        'comment' =>'DataType 이 ENUM,DECIMAL 일경우 쉼표을 기준으로 길이(크기)를 입력하세요'
    ),
    'is_null' => array(
        'title' => 'NULL',
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
