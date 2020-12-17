<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.1
----------------------------------------------------------*/
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilConfig;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\Files\FilesDownload;
use Fus3\R\R;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->useGET();

#Validation Check 예제
$form = new ReqForm();
$form->chkEngNumUnderline('token', '토큰',$req->token, true);
$form->chkEngNumUnderline('doc_id', '도큐멘트ID',$req->doc_id, true);
$form->chkEngNumUnderline('id', '식별번호',$req->id, true);

# resources
R::parserResourceDefinedID('tables');

try{
	$controller = new UtilController($req->fetch());
	$controller->on('uploadable');
	$controller->run('www');

	# model
	$model = new UtilModel($controller->uploadable);
	$loop = array();

	# 접근권한
	chk_authority($controller->uploadable['authority']);
	
	# class
	$db = new DbMySqli();
	$filesSizeConvert = new FilesSizeConvert();

	# query
	$qry = sprintf(
		"SELECT id,directory,sfilename,ofilename FROM `%s` WHERE id='{req.id}' AND `extract_id`='%s' ORDER BY `id` ASC",
			$model->table,
				$req->id,
					$req->token
	);
	$rlt = $db->query($qry);
	$file_row = $rlt->fetch_assoc();
	if(!$file_row || !isset($file_row['id'])){
		history_go(R::$sysmsg['w_not_have_permission']);
	}

	#다운로드 URL 생성
	$dir =_ROOT_PATH_.$file_row['directory'];

	# 성공
	$chset_savename = ($app->platform == 'Windows')  ? iconv('utf-8','euc-kr', $file_row['ofilename']) : $file_row['ofilename'];
	$filesDownload = new FilesDownload($dir,$file_row['sfilename']);
	$filesDownload->download($chset_savename);
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>
