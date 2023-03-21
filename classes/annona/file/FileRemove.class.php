<?php
namespace Flex\Annona\File;

use Flex\Annona;
use Flex\Annona\Dir\DirObject;

# purpose : 파일삭제
final class FileRemove extends DirObject
{
    public array $list = [];

	final function __construct(string $dir) {
        parent::__construct($dir);
	}

    # 디렉토리내 파일 찾기
	public function find (string $pattern, array $nothing=['html','md','php']) : FileRemove
	{		
        # 디렉토리인지 체크
        if($this->isDir($this->directory)){
            $this->list = $this->findFiles($pattern,$nothing);            
        }

    return $this;
    }

    # 파일삭제
    public function remove() : void
    {
        if(count($this->list))
        {
            foreach($this->list as $filename){
                \Flex\Annona\Log::d($this->directory.'/'.$filename);
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