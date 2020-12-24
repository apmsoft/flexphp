<?php
namespace Flex\Files;

# 파일 용량을 알아보기 쉽도록 변환
class FilesSizeConvert
{
	private $filename;
	private $filesize_bytes = 0;
	private $convert_type = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');

	#@ void
	# 파일전체 경로
	public function __construct(string $filenamez=''){
		if(!$filenamez) throw new ErrorException(R::$sysmsg['e_filenotfound']);
		if(!file_exists($filenamez)) throw new ErrorException(R::$sysmsg['e_filenotfound']);

		$this->filename = $filenamez;
		$this->filesize_bytes = filesize($this->filename);
	}

	#@ void
	#  bytes : int
	public function setFileSizeBytes($bytes) : void{
		if(!empty($bytes))
			$this->filesize_bytes = $bytes;
	}

	#@ return int
	public function getFileSizeBytes() : int{
		return $this->filesize_bytes;
	}

	#@ return String
	public function getFileSizeConvert() : string{
		$result = "0";
		if(!empty($this->filesize_bytes)){
	        $e = floor(log($this->filesize_bytes)/log(1024));
	        $result = sprintf('%.2f '.$this->convert_type[$e], ($this->filesize_bytes/pow(1024, floor($e))));
	    }
	return $result;
	}
}
?>
