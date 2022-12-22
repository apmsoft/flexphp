<?php
namespace Flex\Dir;

use Flex\Dir\DirInfo;

# 디렉토리 목록 및 디렉토리에 해달하는 파일 가져오기
class DirObject extends DirInfo{
    public function __construct(string $dir){
        parent::__construct($dir);
    return $this;
    }

    #@ return array
    # 특정폴더안에 있는 모든 파일 및 폴더명을 넘겨받는다.
    # nothing = array("","gif","html")"포함 시키고 쉽지 않은 폴더 제외 및 파일명 제외"
    public function findFiles(string $pattern='*', Array $nothing=array()) : Array
    {
        $result = array();
        $files= glob($this->directory.DIRECTORY_SEPARATOR.$pattern);
        if(is_array($files))
        {
            foreach($files as $filename){
                if (is_file($filename)){
                    $short_filename = basename($filename);
                    $count= strrpos($short_filename,'.');
                    $file_extension= strtolower(substr($short_filename, $count+1));
                    if(!in_array($file_extension, $nothing)) $result[] = $short_filename;
                }
            }
        }
    return $result;
    }

    #@ return array
    # 특정폴더안에 있는 모든 폴더명을 넘겨받는다.
    # nothing = array("디렉토리명")"포함 시키고 쉽지 않은 폴더 제외"
    public function findFolders(Array $nothing=array()) : Array
    {
        $result = array();
        $dirs= glob($this->directory.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
        if(is_array($dirs))
        {
            foreach($dirs as $dirname){
                if ($this->isDir($dirname)){
                    $short_dirname = basename($dirname);
                    if(!in_array($short_dirname, $nothing)) $result[] = $short_dirname;
                }
            }
        }
    return $result;
    }
}
?>
