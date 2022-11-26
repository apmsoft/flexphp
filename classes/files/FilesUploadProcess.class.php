<?php
namespace Flex\Files;

use Flex\R\R;
use Flex\Log\Log;
use Flex\Req\Req;

class FilesUploadProcess
{
    private $request;

    public function __construct(Req $request){
        $this->request = $request;
    }

    public function doProcess (array $config)
    {
        # convert object
        $this->request->usePOST();
        $req = (object) $this->request->fetch();

        # Validation Check 예제
        try{
            $form = new \Flex\Req\ReqForm();
            $form->chkNull('extract_id', $config['columns']['extract_id'],$req->extract_id, true);
        }catch(\Exception $e){
            return json_decode($e->getMessage(),true);
        }

        # Model
        $model = new \Flex\Util\UtilModel($config['fileupload']);
        $model->upfilename = 'filepond';
        $_UPFILES = $_FILES[$model->upfilename];
        
        Log::d('client filename : '.    $_UPFILES['name']);
        Log::d('client mediaType : '.   $_UPFILES['type']);
        Log::d('client size : '.        $_UPFILES['size']);
        Log::d('client error : '.       $_UPFILES['error']);

        if($_UPFILES['error'] > 0){
            Log::e(self.getErrorMsg($_UPFILES['error']));
            return ['result'=>'false','msg_code'=>'e_upload_error','msg'=>self.getErrorMsg($_UPFILES['error'])];
        }

        # 업로드 파일인지 체크
        if(!self::is_upload_files($_UPFILES['tmp_name'])){
            Log::e(R::$sysmsg['e_not_uploaded_file']);
			return ['result'=>'false','msg_code'=>'e_not_uploaded_file','msg'=>R::$sysmsg['e_not_uploaded_file']];
        }

        # 필터 및 복사
        try{
            $utilFileUpload = new \Flex\Util\UtilFileUpload([
                'file_extension' => $config['fileupload']['file_extension'],
                'file_maxsize'   => $config['fileupload']['file_maxsize'],
                'extract_id'     => $req->extract_id
            ],[
                'filename'  => $_UPFILES['name'],
                'mediaType' => $_UPFILES['type'],
                'size'      => $_UPFILES['size'],
                'error'     => $_UPFILES['error']
            ]);
            $savefilename = $utilFileUpload->filter();

            // 복사하기
			if(!self::move_upload_files($_UPFILES['tmp_name'],$utilFileUpload->upload_dir.'/'.$savefilename)){
                Log::e('파일복사 실패');
                return ['result'=>'false','msg_code'=>'e_not_uploaded_file','msg'=>R::$sysmsg['e_not_uploaded_file']];
            }

            # 각도조정
            #$utilFileUpload->chkOrientation();

            # return
            return [
                'result'=> 'true',
                'msg'   => [
                    'sfilename' => $savefilename,
                    'filesize'  => $_UPFILES['size'],
                    'file_type' => (preg_match('/(gif|jpeg|jpg|png)/',$utilFileUpload->getExtName()))?'image/'.$utilFileUpload->getExtName():'application/'.$utilFileUpload->getExtName(),
                    'ofilename' => $utilFileUpload->cleansEtcWords($_UPFILES['name'])
                ]
            ];            
        }catch(\Exception $e){
            Log::d($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    private function getErrorMsg($upload_error_number) : string
    {
        $msg = '';
        switch($upload_error_number){
            case 1:
            case 2:$msg=R::$sysmsg['e_upload_max_filesize']; break;
            case 3:$msg=R::$sysmsg['e_partially_uploaded']; break;
            case 4:$msg=R::$sysmsg['e_no_was_uploaded']; break;
            case 6:$msg=R::$sysmsg['e_miss_temp_folder']; break;
            case 7:$msg=R::$sysmsg['e_failed_write_disk']; break;
            case 8:$msg=R::$sysmsg['e_upload_stopped']; break;
            default : $msg=R::$sysmsg['e_not_uploaded_file'];
        }
    return $msg;
    }

    #@ return boolean
	# 업로드된 파일인지 체크
	private function is_upload_files(string $tmpfile):bool{
		if(!is_uploaded_file($tmpfile)) return false;
	return true;
	}

	#@ return String boolean
	# 업로드 파일 복사하기
	public function move_upload_files(string $tmpfile, string $sfilename): bool{
		if(!move_uploaded_file($tmpfile, $sfilename)) return false;
	return true;
	}
}
?>