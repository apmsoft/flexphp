<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Ftp\Ftp;

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
$form->chkEngNumUnderline('tname', '테이블명', $req->tname, true);
$form->chkEngNumUnderline('newtname_key', '새 테이블명 키', $req->newtname_key, true);
$form->chkEngNumUnderline('newtname_val', '새 테이블명 ', $req->newtname_val, true);

if($req->newtname_key == $req->newtname_val){
    out_json(array('result'=>'false','msg_code'=>'same_name','msg'=>'Table Name... You cannot use the same name.'));
}

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();
$model->table = R::$tables[$req->tname];   # 테이블명
$model->chkfilename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR."tables_"._LANG_.'.json';
$model->filename = (file_exists($model->chkfilename)) ? $model->chkfilename : 'tables';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._QUERY_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# check key
if(isset(R::$tables[$req->newtname_key])){
    out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
}

# db
$db = new DbMySqli();

# 실제 테이블이 있는지 체크
$realtable_qry = sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s' LIMIT 1", $req->newtname_val);
if($realtable_rlt = $db->query($realtable_qry)){
    $realtable_row = $realtable_rlt->fetch_assoc();
    if(isset($realtable_row['COLUMN_NAME'])){
        out_json(array('result'=>'false','msg_code'=>'w_duplicate_id','msg'=>'[table name]'.R::$sysmsg['w_duplicate_id']));
    }
}

# 테이블 복사 및 새로 만들기
$createtable_qry = sprintf("CREATE TABLE IF NOT EXISTS `%s` LIKE `%s`", $req->newtname_val, R::$tables[$req->tname]);
$db->query($createtable_qry);

# 새 테이블 셋팅
R::$tables[$req->newtname_key] = $req->newtname_val;

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
			'msg'    =>R::$sysmsg['v_insert']
		));
    }
}else{
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}
?>
