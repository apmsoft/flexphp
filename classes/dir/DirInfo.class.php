<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 2011-07-11
----------------------------------------------------------*/
namespace Fus3\Dir;

# purpose : 디렉토리 관련
class DirInfo
{
	protected $dirpath;
	const permission	= 0707;

	public function __construct($dir){
		$this->dirpath = $dir;
	}

	# 복수 폴더 만들기
	public function makesDir()
	{
		if(strpos($this->dirpath, '/') !==false)
		{
			$dir_args = explode('/', str_replace(_ROOT_PATH_.'/','',$this->dirpath));
			$current_dir = _ROOT_PATH_;
			if(is_array($dir_args)){
				foreach($dir_args as $folder){
					$current_dir = $current_dir.'/'.$folder;
					if(!$this->makeDirectory($current_dir))
						throw new ErrorException(R::$sysmsg['e_filenotfound']);
				}
			}
		}
	}

	#@ return boolean
	# 폴더 만들기
	public function makeDirectory($dir)
	{
		$result = true;
		# compile_dirname 폴더 이전 경로 생성
		if(!self::isDir($dir)){
			if(!mkdir($dir,self::permission)) $result= false;
			if(!chmod($dir,self::permission)) $result= false;
			#if(!@chown($chkpath,getmyuid())) $result= false; break;
		}
	return $result;
	}

	# 디렉토리인지 확인
	public function isDir($dir){
		if(!is_dir($dir)) return false;
	return true;
	}
}
?>
