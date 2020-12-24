<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 2010-04-13
----------------------------------------------------------*/
namespace Flex\Files;

use Flex\Out\Out;
use Flex\R\R;

# parnet : Files
# purpose : 파일 다운로드
class FilesDownload
{
	private $filename;

	public function __construct(string $dirs, string $filenamez){
		if(!$filenamez)
			Out::prints(R::$sysmsg['e_filenotfound']);

		#디렉토리에 특수문자 체크
		if (!preg_match("/[^a-z0-9_-]/i",$dirs)){
		   Out::prints(R::$sysmsg['e_directory_symbol']);
        }

		# 특수문자 체크
        if (preg_match("/[^\xA1-\xFEa-z0-9._-]|\.\./i",urldecode($filenamez))){
        	Out::prints(R::$sysmsg['e_filename_symbol']);
        }

		# 서버에 파일 존애 여부 체크
		if(!file_exists($dirs.'/'.$filenamez))
			Out::prints(R::$sysmsg['e_filenotfound']);

		# 파일 풀네임
		$this->filename = $dirs.'/'.$filenamez;
	}

	# header
	public function download($title) : void{
		header("Content-type:application/octet-stream");
        header("Cache-control: private");
        header("Content-Disposition:attachment;filename=\"".$title."\"");
        header("Content-Transfer-Encoding:binary");
        header("Pragma:no-cache");
        //header("Expires:0");

        if(is_file($this->filename)) $fp=fopen($this->filename,'rb');
        if(!fpassthru($fp)) fclose($fp);
        exit;
	}
}
?>
