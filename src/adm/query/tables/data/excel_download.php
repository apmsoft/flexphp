<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Preference\PreferenceInternalStorage;
use Fus3\Files\FilesDownload;

# config
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
    history_go(R::$sysmsg['e_db_unenabled']);
}

# model
$model = new UtilModel();
$model->table       = R::$tables[$req->tname];   # 테이블명
$model->where       = '';          # WHERE 조건문
$model->columns     = '*';
$model->primary_key = '';

$model->dir         = _ROOT_PATH_.'/'._DATA_;
$model->dwnfilename = sprintf("%s%d.csv", $req->tname,time());
$data = array();

# db
$db = new DbMySqli();

$columns = array();
$rlt = $db->query(sprintf("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->table));
while($row = $rlt->fetch_assoc())
{
	if($model->primary_key ==''){
		if($row['COLUMN_KEY'] == 'PRI'){
			$model->primary_key = $row['COLUMN_NAME'];
		}
    }
	$columns[] = $row['COLUMN_NAME'];
}

$data[] = $columns;

# query
$qry = sprintf("SELECT %s FROM `%s` %s ORDER BY %s DESC", 
    $model->columns, 
        $model->table, 
            ($model->where) ? 'WHERE '.$model->where : '', 
                $model->primary_key
);
$rlt = $db->query( $qry );

# while
while( $row=$rlt->fetch_assoc() )
{
    $item = array();
	foreach($row as $k=>$v){
		if(is_string($v)){
			$v = ($app->platform == 'Windows') ? iconv('utf-8', 'euc-kr', $v) : $v;
		}

		$item[] = $v;
    }
    
	if(count($item) > 0){
		$data[] = $item;
	}
}

# 성공
$chset_savename = ($app->platform == 'Windows')  ? iconv('utf-8','euc-kr', $req->tname) : $req->tname;

# save
$pfInternalStorage = new PreferenceInternalStorage($model->dir.'/'.$model->dwnfilename, 'w');
$pfInternalStorage->writeInternalStorageCSV($data);

# 성공
$filesDownload = new FilesDownload($model->dir,$model->dwnfilename);
$filesDownload->download($chset_savename.'.csv');

# 삭제
unlink($model->dir.'/'.$model->dwnfilename);
?>
