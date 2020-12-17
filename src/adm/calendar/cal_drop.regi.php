<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://fancy-up.tistory.com
| @Editor	: Sublime Text 3 (기본설정)
| @UPDATE	: 0.5
| @TITLE 	: php 개발 가이드 (종합)
----------------------------------------------------------*/
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
if($auth->level <_AUTH_SUPERADMIN_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkUserid('id', '일정[게시판]ID', $req->id, true);

# Model
$model = new UtilModel();
$model->category = 'calendar';
$model->manifest_document = array();
$model->new_table = 'fu2_cal_'.$req->id;
$model->config_temp_path   = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->category.'_'.$req->id.'.json';
$model->config_real_path   = _FTP_DIR_.DIRECTORY_SEPARATOR._CONFIG_.DIRECTORY_SEPARATOR.$model->category.'_'.$req->id.'.json';
$model->table_temp_path    = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.'tables.json';
$model->table_real_path    = _FTP_DIR_.DIRECTORY_SEPARATOR._QUERY_.DIRECTORY_SEPARATOR.'tables.json';
$model->manifest_temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.'manifest.json';
$model->manifest_real_path = _FTP_DIR_.DIRECTORY_SEPARATOR._RES_.DIRECTORY_SEPARATOR.'manifest.json';

# db 선언 및 접속
$db = new DbMySqli();

# resource
R::parserResourceDefinedID('tables');
R::parserResourceDefinedID('manifest');

# 테이블이 있는지 체크
$tables = array();
if($rlt = $db->query(sprintf("SHOW TABLES FROM `%s`", _DB_NAME_))){
    while($row = $rlt->fetch_row()){
        $tables[] = $row[0];
    }
}

# 없으면 생성 : 테이블 drop
if(in_array($model->new_table, $tables)){
    $db->query(sprintf("DROP TABLE %s", $model->new_table));
}

# ftp 클랙스
$ftp = new Ftp();

# tables.json remove
if(isset(R::$tables[$req->id])){
    if($file_table = $ftp->open_file_read($model->table_temp_path, $model->table_real_path))
    {
        # 쓰기
        unset(R::$tables[$req->id]);
        $context_tables = json_encode(R::$tables);
        if(!$ftp->open_file_write($model->table_temp_path, $model->table_real_path, $context_tables)) {
            out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!11'));
        }
    }else{
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!12'));
    }
}

# manifest.json remove
if(isset(R::$manifest['feature']['document'][$req->id]))
{	
	# document | uploadfiles
	unset(R::$manifest['feature']['document'][$req->id]);
    unset(R::$manifest['feature']['uploadfiles'][$req->id]);

	if($file_manifest = $ftp->open_file_read($model->manifest_temp_path, $model->manifest_real_path))
    {
        # 쓰기
        $context_manifest = json_encode(R::$manifest);
        if(!$ftp->open_file_write($model->manifest_temp_path, $model->manifest_real_path, $context_manifest)) {
            out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!21'));
        }
    }else{
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!22'));
    } 
}

# remove file
$model->del_filename = $model->category.'_'.$req->id.'.json';
$ftp->delete_file(_FTP_DIR_._CONFIG_, $model->del_filename);

# adm log
Log::init(R::$tables['adm_log']);
Log::d( sprintf("cal DROP %s ", $req->id) );

# output
out_json(array(
	'result' =>'true',
	'msg'    =>R::$sysmsg['v_delete']
));
?>
