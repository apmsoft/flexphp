<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
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
$form->chkEngNumUnderline('vkey', 'id', $req->vkey, true);
$form->chkEngNumUnderline('category', '카테고리', $req->category, true);
$form->chkNull('title', '타이틀', $req->title, true);
$form->chkAlphabet('uploadable_enable', '첨부파일 활성화여부', $req->uploadable_enable, false);

# config 데이터 체크
$config_count = 0;
for($ci=0; $ci<10; $ci++){
	$cikey = 'config'.$ci.'_key';
	$cival = 'config'.$ci.'_val';

	$vci_key = $req->{$cikey};
	$vci_val = $req->{$cival};

	if($vci_key){
		$form->chkEngNumUnderline($cikey, '실행파일키(config)', trim($vci_key), false);
		$form->chkEngNumUnderline($cival, '실행파일(json)', trim($$vci_val), false);

		$config_count++;
	}	
}

// if($config_count < 1){
// 	out_json(array('result'=>'false','msg_code'=>'e_null','msg'=>'실행 키와 실행 파일은 최소 한개이상 ',R::$sysmsg['e_null']));
// }

# 첨부파일 데이터 체크
if($req->uploadable_enable !='false')
{
	$form->chkEngNumUnderline('uploadable_table', '첨부파일용 테이블명', $req->uploadable_table, true);
	$form->chkEngNumUnderline('uploadable_authority', '첨부파일 권한', $req->uploadable_authority, true);
	$form->chkNumber('uploadable_thumbnail_width', '이미지 사이즈(width)', $req->uploadable_thumbnail_width, true);
	$form->chkNumber('uploadable_thumbnail_height', '이미지 사이즈(height)', $req->uploadable_thumbnail_height, true);
	$form->chkNumber('uploadable_middle_width', '이미지 사이즈(width)', $req->uploadable_middle_width, true);
	$form->chkNumber('uploadable_middle_height', '이미지 사이즈(height)', $req->uploadable_middle_height, true);
	$form->chkNull('uploadable_file_extension', '첨부파일 확장자', $req->uploadable_file_extension, true);
	$form->chkNumber('uploadable_file_maxsize', '첨부파일사이즈(Mbyte)', $req->uploadable_file_maxsize, true);
	$form->chkNumber('uploadable_number_files_allowed', '첨부파일 최대허용 갯수', $req->uploadable_number_files_allowed, true);
}

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'config.php';

# Model
$model = new UtilModel();
$model->chkfilename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR."manifest_"._LANG_.'.json';
$model->filename = (file_exists($model->chkfilename)) ? $model->chkfilename : 'manifest';
$model->temp_path = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$model->filename.'.json';
$model->real_path = _FTP_DIR_._RES_.DIRECTORY_SEPARATOR.$model->filename.'.json';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest');

# check
if(!isset(R::$manifest[$req->vkey])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# config
$set_config = array();
for($ci=0; $ci<10; $ci++){
	$cikey = 'config'.$ci.'_key';
	$cival = 'config'.$ci.'_val';

	$vci_key = $req->{$cikey};
	$vci_val = $req->{$cival};

	if(trim($vci_key)){
		$set_config[$vci_key] = $vci_val;
	}
}

# uploadable
$set_uploadable = ($req->uploadable_enable !='false') ? array
(
	'table' => sprintf('{__TABLES__.%s}', $req->uploadable_table),
	'authority' => $req->uploadable_authority,
	'image_size' => array
	(
		'thumbnail' => array
		(
			'width' => $req->uploadable_thumbnail_width,
			'height' => $req->uploadable_thumbnail_height
		),
		'middle' => array
		(
			'width' => $req->uploadable_middle_width,
			'height' => $req->uploadable_middle_height
		)
	),
	'file_extension' => $req->uploadable_file_extension,
	'file_maxsize' => $req->uploadable_file_maxsize,
	'number_files_allowed' => $req->uploadable_number_files_allowed
) : null;

$set_manifest_data = array(
	'category' => $req->category,
    'title' => $req->title,
    'config' => $set_config,
    'uploadable' => $set_uploadable
);

# field
R::$manifest[$req->vkey] = $set_manifest_data;

# ftp 클랙스
$ftp = new Ftp();

# 파일 저장
if($file = $ftp->open_file_read($model->temp_path, $model->real_path))
{
    # 쓰기
    $context = json_encode(R::$manifest,JSON_UNESCAPED_UNICODE);
    if(!$ftp->open_file_write($model->temp_path, $model->real_path, $context)) {
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
    }else{
    	# output
		out_json(array(
			'result' =>'true',
			'msg'    =>R::$sysmsg['v_update']
		));
    }
}else{
	out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!'));
}
?>
