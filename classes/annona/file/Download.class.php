<?php
namespace Flex\Annona\File;

use Flex\Annona;
use Flex\Annona\File\FileSize;
use \Exception;

# parnet :
# purpose : 파일다운로드
final class Download extends FileSize
{
	public const __version = '1.1';

	# 다운로드 허용 확장자
	private array $allowed_filetypes = ['pdf','xls','xlsx','doc','docx','zip','hwp','ppt','pptx','jpg','jpeg','png','gif'];
	public string $file_extension    = '';
	private string $title = '';
	private array $headers           = [
		'Content-type'              => 'application/octet-stream',
		'Cache-control'             => 'private',
		"Content-Transfer-Encoding" => "binary",
		"Pragma"                    => "no-cache"
	];

	final public function __construct(string $filenamez){
		parent::__construct($filenamez);
		$this->getExtName();
	}

	# 파일 확장자 추출
	private function getExtName() : void{
		$tmpfile = basename($this->filename);
		$count   = strrpos($tmpfile,'.');
		$this->file_extension = strtolower(substr($tmpfile, $count+1));
	}

	# 다운로드 허용 파일 확장자 등록
	public function setFileTypes(array $allowed_filetypes = []) : Download
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
			throw new \Exception( 'e_extension_not_allowed');
		}

		return file_get_contents($this->filename);
    }

	public function __get(string $propertyName) : mixed
	{
		$result = [];
		if(property_exists(__CLASS__,$propertyName)){
			if($propertyName == 'headers' 
				|| $propertyName == 'allowed_filetypes'
				|| $propertyName == 'title'){
				$result = $this->{$propertyName};
			}
		}
	return $result;
	}

	# header 값 추가 및 변경
	public function __set(string $propertyName, mixed $propertyValue) : void
	{
		if(property_exists(__CLASS__,$propertyName)){
			if($propertyName == 'headers'){
				if(is_array($propertyValue)){
					$this->headers = array_merge($this->headers, $propertyValue);
				}
			}
		}
	}

	# 다운로드파일명 설정
	public function setFileName (string $title) : Download {
		$this->title = $title;
		$this->headers['Content-Disposition'] = sprintf('attachment;filename="%s"', $title);
	return $this;
	}

	public function download() : void
	{
		# file contents
		$file_contents = $this->getContents ();

		# header
		$headers = [];
		foreach($this->headers as $hkey => $hval)
		{
			# header content
			$headerstring = sprintf("%s:%s", $hkey, $hval);

			# append
			$headers[] = $headerstring;
		}

		# output
		foreach($this->headers as $_header){
			header($_header);
		}

		# 출력
        echo $file_contents;
	}
}
?>