<?php
use Fus3\Util\UtilController;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Files\FilesSizeConvert;
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
$form->chkEngNumUnderline('upfilename', '파일명',$req->upfilename, true);

# resources
R::parserResourceDefinedID('tables');

try{
	$controller = new UtilController($req->fetch());
	$controller->on('uploadable');
	$controller->run('www');

	# model
	$model = new UtilModel($controller->uploadable);

	# 접근권한
	chk_authority($controller->uploadable['authority']);
	
	# class
	$db = new DbMySqli();
	$filesSizeConvert = new FilesSizeConvert();

	# 파일업로드
    if(isset($_FILES[$req->upfilename]))
    {
        # 같은 파일명으로 등록 했는지 체크
        if($is_data=$db->get_record('id', $model->table,sprintf("`extract_id`='%s'",$req->token) )){
            $utilMakeThumbnail->removeFile($is_data);
            $db->query(sprintf("DELETE FROM `%s` WHERE `id`=%s", $model->table, $is_data['id']));
        }

        # 필터 및 복사
        $fileupObj = new UtilFileUpload(array(
            'file_extension' =>$model->file_extension,
            'file_maxsize'   =>$model->file_maxsize
        ));
        $file_args=$fileupObj->fileUpload(
            $_FILES[$req->upfilename]['tmp_name'],
            $_FILES[$req->upfilename]['name'],
            $_FILES[$req->upfilename]['size'],
            $_FILES[$req->upfilename]['type'],
            $_FILES[$req->upfilename]['error']
        );

        #디비 저장
        if(is_array($file_args) && count($file_args)>0)
        {
            $db['extract_id']=$req->token;
            $db['regi_date'] =time();
            $db['file_type'] =$file_args['file_type'];
            $db['sfilename'] =$file_args['sfilename'];
            $db['ofilename'] =$file_args['ofilename'];
            $db['file_size'] =$file_args['file_size'];
            $db['directory'] =$file_args['directory'];
            $db->insert($model->table);

            # 저장된 파일정보
            $file_row = $db->get_record('id,file_type,directory,sfilename,ofilename', $model->table, sprintf("extract_id='%s'",$req->token) );
            if(isset($file_row['id'])){
                # 썸네일만들기
                $utilMakeThumbnail->makeThumbnail($file_row, $model->image_size['thumbnail'], $model->image_size['middle']);
            }

            # 이미지일 경우 이미지 사이즈 구하기
            $image_size= array();
            $file_type = (substr($file_args['file_type'],0,5)=='image') ? 'image' : 'application';
            if(!strcmp($file_type,'image')){
                $image_size = @getimagesize(_ROOT_PATH_.$file_args['directory'].'/'.$file_args['sfilename']);
            }

            # result
            $uploaded_files[] = array(
                'hosturl'   =>_SITE_HOST_,
                'directory' =>$file_args['directory'],
                'fullname'  =>$file_args['directory'].'/'.$file_args['sfilename'],
                'sfilename' =>$file_args['sfilename'],
                'ofilename' =>$file_args['ofilename'],
                'file_type' =>$file_type,
                'image_size'=>$image_size
            );
        }
        # 성공
        out_json($uploaded_files);
    }else{
        out_json(array('result'=>'false','msg_code'=>'e_no_was_uploaded','msg'=>R::$sysmsg['e_no_was_uploaded']));
    }
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>