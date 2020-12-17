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
$form->chkEngNumUnderline('data_type', '데이터타입', $req->data_type, true);

# 데이터 타입에 따른 데이터 길이
switch($req->data_type){
    case 'ENUM':
        $form->chkNull('data_length', '데이터길이(크기)', $req->data_length, true);
        if(strpos($req->data_length,',') ===false){
            out_json(array('result'=>'false','fieldname'=>'data_length','msg_code'=>'w_data_length','msg'=>'데이터길이(크기)는 쉼표(,)를 기준으로 입력하세요.'));
        }
        $tmp_datalen = explode(',', $req->data_length);
        $tmp_datalen_arg = array();
        if(is_array($tmp_datalen)){
            foreach($tmp_datalen as $dk=>$dv){
                $tmp_datalen_arg[] = sprintf("'%s'",$dv);
            }
            $req->data_length = implode(",", $tmp_datalen_arg);
        }
    break;
    case 'DECIMAL':
        $form->chkNull('data_length', '데이터길이(크기)', $req->data_length, true);
        if(strpos($req->data_length,',') ===false){
            out_json(array('result'=>'false','fieldname'=>'data_length','msg_code'=>'w_data_length','msg'=>'데이터길이(크기)는 쉼표를 기준으로 입력하세요.'));
        }
    break;
    case 'DOUBLE':
    case 'TEXT':
    case 'MEDIUMTEXT':
    case 'TINYTEXT':
    case 'LONGTEXT':
    case 'DATE':
    case 'DATETIME':
    case 'TIME':
    case 'YEAR':
    case 'TIMESTAMP':
    case 'JSON':
    case 'BLOB':
        $req->data_length = '';
    break;
    default :
        $form->chkNumber('data_length', '데이터길이(크기)', $req->data_length, true);
}

# unsigned
switch($req->data_type){
    case 'INT':
    case 'MEDIUMINT':
    case 'SMALLINT':
    case 'TINYINT':
    case 'DOUBLE':
    case 'BIGINT':
    break;
    default : 
        $req->unsigned = '';
}

# null 
$form->chkEngNumUnderline('is_null', 'Data NULL', $req->is_null, true);
if($req->is_null == 'NOT NULL')
{
    // chk default value
    switch($req->data_type){
        case 'INT':
        case 'MEDIUMINT':
        case 'SMALLINT':
        case 'TINYINT':
        case 'ENUM':
        case 'DOUBLE':
        case 'BIGINT':
            $form->chkNull('data_default', 'Data Default Value(데이터 기본값)', $req->data_default, true);
        break;
    }

    if($req->data_default !=''){
        $req->is_null = sprintf("NOT NULL DEFAULT '%s'",$req->data_default);
    }else{
        $req->is_null = "NOT NULL";
    }
}

# 코멘트
$form->chkNull('comment', 'Data Comment', $req->comment, true);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명
$columns = array();

# check key
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# db
$db = new DbMySqli();

# 실제 테이블이 있는지 체크
$realtable_qry = sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s' LIMIT 1", $model->table);
$realtable_rlt = $db->query($realtable_qry);
if(!$realtable_rlt){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

while($realtable_info = $realtable_rlt->fetch_assoc()){
    $columns[] = $realtable_info['COLUMN_NAME'];
}

# 이미 같은 퀄럼이 있는지 체크
if(array_search($req->column_name, $columns) > -1){
    out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
}

# 퀄럼 정보 변경
# ALTER TABLE `swcms_db`.`test` 
# CHANGE COLUMN `signdate` `signdate` JSON NOT NULL ,
# ADD COLUMN `dfds` VARCHAR(45) NOT NULL AFTER `test`,
# ADD COLUMN `edd` VARCHAR(45) NOT NULL DEFAULT 'ㅇㄴ' COMMENT '코멘트 ' AFTER `dfds`;
$change_qry = sprintf("ALTER TABLE `%s` ADD COLUMN `%s` %s%s %s %s COMMENT '%s'", 
    $model->table,
        $req->column_name,
            $req->data_type,
                ($req->data_length) ? sprintf("(%s)",$req->data_length) : '',
                    $req->data_unsigned,
                        $req->is_null,
                            $req->comment
                        );
if(!$rlt = $db->query($change_qry)){
    out_json(array('result'=>'false','msg_code'=>'e_change_column','msg'=>'Error e_change_column'));
}

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_update']
));
?>
