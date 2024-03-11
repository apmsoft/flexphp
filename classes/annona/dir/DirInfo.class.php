<?php
namespace Flex\Annona\Dir;

# purpose : 디렉토리 관련
class DirInfo
{
	public const __version = '1.1.0';
	public string $directory;
	const permission	= 0707;

	public function __construct(string $dir)
	{
		$this->directory = $dir;
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
					if(!self::isDir($current_dir)){
						if(!mkdir($current_dir,self::permission)) throw new ErrorException('e_filenotfound');
						if(!chmod($current_dir,self::permission)) throw new ErrorException('e_filenotfound');
					}
				}
			}
		}
	}

	# 폴더 만들기
	public function makeDirectory(string $dir) : bool
	{
		$result = true;
		$directory = $this->directory.'/'.$dir;
		# compile_dirname 폴더 이전 경로 생성
		if(!self::isDir($directory)){
			if(!mkdir($directory,self::permission)) $result= false;
			if(!chmod($directory,self::permission)) $result= false;
			#if(!@chown($chkpath,getmyuid())) $result= false; break;
		}
	return $result;
	}

	# 디렉토리인지 확인
	protected function isDir(string $dir) : bool{
		if(!is_dir($dir)) return false;
	return true;
	}
}
?>
