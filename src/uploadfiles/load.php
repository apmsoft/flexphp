<?php
use Fus3\Util\UtilController;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Files\FilesSizeConvert;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# req
$req = new Req;
$req->useGET();

#Validation Check 예제
$form = new ReqForm();
$form->chkEngNumUnderline('token', '토큰',$req->token, true);
$form->chkEngNumUnderline('doc_id', '도큐멘트ID',$req->doc_id, true);
#$form->chkEngNumUnderline('upfilename', '파일명',$req->upfilename, true);

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
		"SELECT id,file_type,directory,sfilename,ofilename,file_size FROM `%s` WHERE `extract_id`='%s' ORDER BY `id` ASC",
			$model->table,
				$req->token
	);
	$rlt = $db->query($qry);
	while($row = $rlt->fetch_assoc())
	{
		# 이미지일 경우 이미지 사이즈 구하기
		$image_size     = array();
		$file_type      = (substr($row['file_type'],0,5)=='image') ? 'image' : 'application';
		$file_extension = '';
		$file_src 		= '';
		if(!strcmp($file_type,'image')){
			$image_size = @getimagesize(_ROOT_PATH_.$row['directory'].'/'.$row['sfilename']);
			$file_src =$row['directory'].'/s/'.$row['sfilename'];
		}else{
			$count= strrpos($row['sfilename'],'.');
			$file_extension= strtolower(substr($row['sfilename'], $count+1));
			$file_src =$row['directory'].'/'.$row['sfilename'];
		}

		# 파일사이즈
		$filesSizeConvert->setFileSizeBytes($row['file_size']);

		// model
		$listModel = new UtilModel($row);
		$listModel->hosturl        =_SITE_HOST_;
		$listModel->fullname       =$file_src;
		$listModel->file_type      =$file_type;
		$listModel->file_extension =$file_extension;
		$listModel->image_size     =$image_size;
		$listModel->file_size      =$filesSizeConvert->getFileSizeConvert();

		# fetch
		$loop[] = $listModel->fetch();
	}

	# output
	out_json(array(
		'result' => 'true',
		'msg' => $loop
	));
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>