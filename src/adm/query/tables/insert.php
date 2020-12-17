<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Ftp\Ftp;
use Fus3\Db\DbMySqli;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ftp.php';

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
$form->chkEngNumUnderline('vkey', 'id', $req->vkey, true);
$form->chkNull('vval', 'value', $req->vval, true);
$form->chkNull('title', '타이틀', $req->title, true);

# Model
$model = new UtilModel();
$model->chkfilename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR."tables_"._LANG_.'.json';
$model->filename = (file_exists($model->chkfilename)) ? $model->chkfilename : 'tables';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._QUERY_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# check 키가 있는지 체크
if(isset(R::$tables[$req->vkey])){
    out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
}

# 실제 테이블이 있는지 체크
$realtable_qry = sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s' LIMIT 1", $req->vval);
if($realtable_rlt = $db->query($realtable_qry)){
    $realtable_row = $realtable_rlt->fetch_assoc();
    if(isset($realtable_row['COLUMN_NAME'])){
        out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>'[table name]'.R::$sysmsg['w_duplicate_id']));
    }
}

# field
R::$tables[$req->vkey] = $req->vval;

# scheme
$create_table_scheme = sprintf(
	"CREATE TABLE `%s` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '고유번호',PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='%s';",
	$req->vval, $req->title
);
if(!$create_rlt = $db->query($create_table_scheme)){
	out_json(array('result'=>'false','msg_code'=>'w_create_table_fail','msg'=>'fail, Create Table'));
}


# ftp 클랙스
$ftp = new Ftp();

# 파일 저장
if($file = $ftp->open_file_read($model->temp_path, $model->real_path))
{
    # 쓰기
    $context = json_encode(R::$tables,JSON_UNESCAPED_UNICODE);
    if(!$ftp->open_file_write($model->temp_path, $model->real_path, $context)) {
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
    }else{
    	# output
		out_json(array(
			'result' =>'true',
			'msg'    =>R::$sysmsg['v_write']
		));
    }
}else{
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}
?>
