<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.1
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	history_go(R::$sysmsg['w_not_have_permission']);
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	history_go(R::$sysmsg['w_not_have_permission']);
}

# 레벨체크
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	history_go(R::$sysmsg['w_not_have_permission']);
}

# 변수
$req = new Req;
$req->usePOST();

# Validation Check
$form = new ReqForm();
$form->chkEngNumUnderline('doc_id', 'DOC ID',$req->doc_id, true);

# Model
$model = new UtilModel();
$model->columns     = '';
$model->dir         = _ROOT_PATH_.'/'._DATA_;
$model->dwnfilename = sprintf("m%d.csv", time());
$data               = array();

# column 만들기
$req_forms = $req->fetch();
$_temp_columns = array();
if(is_array($req_forms)){
	foreach($req_forms as $column_name => $column_val){
		if($column_val == 'y'){
			$_temp_columns[] = $column_name;
		}
	}
	if(count($_temp_columns)<1){
		history_go('하나이상 선택하세요');
	}

	$model->columns = implode(',', $_temp_columns);
}

# resource
// R::init($req->lang);
R::parserResourceDefinedID('tables');
R::parserResourceDefinedID('manifest');

# manifest doc_id  체크
if(!isset(R::$manifest['feature']['document'][$req->doc_id])){
	history_go(R::$sysmsg['e_doc_id']);
}

$manifest_doc_info = array();
$manifest_doc_info =& R::$manifest['feature']['document'][$req->doc_id];
$table_nickname = $manifest_doc_info['table'];
$model->table = R::$tables[$table_nickname];

# class
$db=new DbMySqli();

# query
$data[] = $_temp_columns;
$qry = sprintf("SELECT %s FROM `%s`", $model->columns, $model->table);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc())
{
	$item = array();
	foreach($row as $k=>$v){
		if(is_string($v)){
			$v = iconv('utf-8', 'euc-kr', $v);
		}

		$item[] = $v;
	}
	if(count($item) > 0){
		$data[] = $item;
	}
}

# adm log
Log::init(R::$tables['adm_log']);
Log::d( sprintf("MEMBER EXCEL DOWNLOAD : %s", __date('now','Y-m-d H:i:s')) );

# save
$pfInternalStorage = new PreferenceInternalStorage($model->dir.'/'.$model->dwnfilename, 'w');
$pfInternalStorage->writeInternalStorageCSV($data);

# 성공
$filesDownload = new FilesDownload($model->dir,$model->dwnfilename);
$filesDownload->download('회원(member)목록_엑셀.csv');
?>
