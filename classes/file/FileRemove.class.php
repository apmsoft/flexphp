<?php
namespace Flex\File;

use Flex\Log\Log;
use Flex\Dir\DirObject;

# purpose : 파일삭제
final class FileRemove extends DirObject
{
    public array $files = [];

	final function __construct(string $dir) {
        parent::__construct($dir);
    return $this;
	}

    # 디렉토리내 파일 찾기
	public function find (string $pattern, array $nothing=['html','md','php']) : FileRemove
	{		
        # 디렉토리인지 체크
        if($this->isDir($this->directory)){
            $this->files = $this->findFiles($pattern,$nothing);            
        }

    return $this;
    }

    # 파일삭제
    public function remove() : void{
        if(count($this->files))
        {
            foreach($this->files as $filename){
                unlink($this->directory.'/'.$filename) or throw new ErrorException('e_file_deletion_failed');
            }
        }
    }

    public function __get(string $propertyName){
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}
?>