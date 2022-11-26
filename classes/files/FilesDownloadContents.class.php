<?php
namespace Flex\Files;

use Flex\Log\Log;

# parnet : 
# purpose : 파일다운로드
final class FilesDownloadContents
{
	private $upload_dir;
	private $ext_args = ['pdf','xlsx','doc','xls','zip','docx','hwp','ppt','pptx'];

	final public function __construct(string $extract_id){
		$this->upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/'.$extract_id;
	}

	#@ return String
	# 파일 확장자 추출
	public function getExtName(string $filename) : string{
		$count = strrpos($filename,'.');
		$file_extension = strtolower(substr($filename, $count+1));
	return $file_extension;
	} 

	public function doDownload (string $filename) : array
	{
		$fullname = $this->upload_dir.'/'.$filename;

		$file_type = 'application';
		$exe = $this->getExtName($filename);
		if(in_array($exe,$this->ext_args)){
			$file_type = 'application';
		}

        # 서버에 파일 존애 여부 체크
		if(!file_exists($fullname)){
            Log::e(R::$sysmsg['e_filenotfound']);
            return ['result'=>'false','msg_code'=>'e_filenotfound','msg'=>R::$sysmsg['e_filenotfound']];
        }

		# 파일타입체크
		$file_contents = '';
		if(!strcmp($file_type,'application'))
		{
			# read
			$file_contents = file_get_contents($fullname);
		}

		return [
            'filename' => $filename,
			'contents' => $file_contents
		];
    }
}
?>