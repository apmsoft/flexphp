<?php
use Fus3\Util\UtilController;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Util\UtilFileUpload;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# req
$req = new Req;
$req->usePOST();

#Validation Check 예제
$form = new ReqForm();
$form->chkEngNumUnderline('token', '토큰',$req->token, true);
$form->chkEngNumUnderline('doc_id', '도큐멘트ID',$req->doc_id, true);
$form->chkNull('op', 'op',$req->op, true);
$form->chkNull('name', 'name',$req->name, true);

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
	
	if($req->op && $req->op =='delete' && $req->name)
	{
		$is_data = $db->get_record('id,sfilename,directory', 
			$model->table, 
				sprintf("extract_id='%s' AND sfilename='%s'",$req->token,$req->name)
		);

		if( isset($is_data['id']) )
		{
			# 디비삭제
			$db->delete($model->table, sprintf("`id`='%u'",$is_data['id']));

			# 파일지우기
			$fileupObj = new UtilFileUpload(array());
			$fileupObj->fileRemove(_ROOT_PATH_.$is_data['directory'].'/s/'.$is_data['sfilename']);
			$fileupObj->fileRemove(_ROOT_PATH_.$is_data['directory'].'/m/'.$is_data['sfilename']);
			$fileupObj->fileRemove(_ROOT_PATH_.$is_data['directory'].'/'.$is_data['sfilename']);

			# output
			out_json(array('result'=>'true','msg'=>'success'));
		}else{
			# output
			out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
		}
	}else{
		# output
		out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
	}
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>