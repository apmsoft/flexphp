<?php
namespace Flex\Files;

use Flex\Log\Log;
use Flex\Files\FilesSize;
use \ErrorException;

# parnet : 
# purpose : 파일다운로드
final class FilesDownload extends FilesSize
{
	# 다운로드 허용 확장자
	private array $allowed_filetypes = ['pdf','xls','xlsx','doc','docx','zip','hwp','ppt','pptx','jpg','jpeg','png','gif'];
	public string $file_extension = '';
	private array $headers = [
		'Content-type:application/octet-stream',
		"Cache-control: private",
		"Content-Transfer-Encoding:binary",
		"Pragma:no-cache"
	];

	final public function __construct(string $filenamez){
		parent::__construct($filenamez);
		self::getExtName();

	return $this;
	}

	# 파일 확장자 추출
	private function getExtName() : void{
		$tmpfile = basename($this->filename);
		$count   = strrpos($tmpfile,'.');
		$this->file_extension = strtolower(substr($tmpfile, $count+1));
	}

	# 다운로드 허용 파일 확장자 등록
	public function setFileTypes(array $allowed_filetypes = []) : FilesDownload
	{
		if(count($allowed_filetypes)){
			$this->allowed_filetypes = $allowed_filetypes;
		}
	return $this;
	}

	public function getContents () : string
	{
		# 다운로드 허용 파일인지 체크
		if(!in_array($this->file_extension,$this->allowed_filetypes)){
			throw new \ErrorException( 'e_extension_not_allowed');
		}

		return file_get_contents($this->filename);
    }

	public function download(string $title, array $headers = []) : void
	{
		# file contents
		$file_contents = self::getContents ();

		# header
        header(sprintf('Content-Disposition:attachment;filename="%s"', $title));
		if(count($headers)){
			foreach($headers as $hv){
				array_push($this->headers, $hv);
			}
		}
		foreach($this->headers as $_header){
			header($_header);
		}

		# 출력
        echo $file_contents;
	}
}
?>