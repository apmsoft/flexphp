<?php
namespace Flex\Files;

use Flex\R\R;
use Flex\Log\Log;
use Flex\Req\Req;
use Flex\Files\FilesUpload;

class FilesUploadProcess extends FilesUpload
{
    private $request;
    const _UP_FILENAME_ = 'filepond';

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

        $_UPFILES = $_FILES[self::_UP_FILENAME_];
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
            parent::__construct(
                [
                    'file_extension' => $config['fileupload']['file_extension'],
                    'file_maxsize'   => $config['fileupload']['file_maxsize'],
                    'extract_id'     => $req->extract_id
                ],[
                    'filename'  => $_UPFILES['name'],
                    'mediaType' => $_UPFILES['type'],
                    'size'      => $_UPFILES['size'],
                    'error'     => $_UPFILES['error']
                ]
            );
            $savefilename = parent::filter();

            // 복사하기
			if(!self::move_upload_files($_UPFILES['tmp_name'],parent::$upload_dir.'/'.$savefilename)){
                return ['result'=>'false','msg_code'=>'e_not_uploaded_file','msg'=>R::$sysmsg['e_not_uploaded_file']];
            }

            # return
            return [
                'result'=> 'true',
                'msg'   => [
                    'sfilename' => $savefilename,
                    'filesize'  => $_UPFILES['size'],
                    'file_type' => (preg_match('/(gif|jpeg|jpg|png)/',parent::getExtName())) ? 'image/'.parent::getExtName() : 'application/'.parent::getExtName(),
                    'ofilename' => parent::cleansEtcWords($_UPFILES['name'])
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