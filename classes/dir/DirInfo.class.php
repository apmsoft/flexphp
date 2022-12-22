<?php
namespace Flex\Dir;

# purpose : 디렉토리 관련
class DirInfo
{
	public string $directory;
	const permission	= 0707;

	public function __construct(string $dir)
	{
		$this->directory = $dir;

	return $this;
	}

	# 복수 폴더 만들기
	public function makesDir() : void
	{
		if(strpos($this->directory, '/') !==false)
		{
			$dir_args = explode('/', str_replace(_ROOT_PATH_.'/','',$this->directory));
			$current_dir = _ROOT_PATH_;
			if(is_array($dir_args)){
				foreach($dir_args as $folder){
					$current_dir = $current_dir.'/'.$folder;
					if(!$this->makeDirectory($current_dir)){
						throw new ErrorException('e_filenotfound');
					}
				}
			}
		}
	}

	# 폴더 만들기
	public function makeDirectory(string $dir) : bool
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
	public function isDir(string $dir) : bool{
		if(!is_dir($dir)) return false;
	return true;
	}
}
?>
