<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 1.0.1
----------------------------------------------------------*/
namespace Fus3\Files;

# 파일 용량을 알아보기 쉽도록 변환
class FilesSizeConvert
{
	private $filename;
	private $filesize_bytes = 0;
	private $convert_type = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');

	#@ void
	# 파일전체 경로
	public function __construct($filenamez=''){
		if(!$filenamez) return false;
		if(!file_exists($filenamez)) return false;

		$this->filename = $filenamez;
		$this->filesize_bytes = filesize($this->filename);
	}

	#@ void
	#  bytes : int
	public function setFileSizeBytes($bytes){
		if(!empty($bytes))
			$this->filesize_bytes = $bytes;
	}

	#@ return int
	public function getFileSizeBytes(){
		return $this->filesize_bytes;
	}

	#@ return String
	public function getFileSizeConvert(){
		$result = "0";
		if(!empty($this->filesize_bytes)){
	        $e = floor(log($this->filesize_bytes)/log(1024));
	        $result = sprintf('%.2f '.$this->convert_type[$e], ($this->filesize_bytes/pow(1024, floor($e))));
	    }
	return $result;
	}
}
?>
