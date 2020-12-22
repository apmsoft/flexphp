<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : apmsoft.tistory.com
| @Editor   : Sublime Text3
| @UPDATE   : 2011-07-11
----------------------------------------------------------*/
namespace Flex\Dir;

use Flex\Dir\DirInfo;

# 디렉토리 목록 및 디렉토리에 해달하는 파일 가져오기
class DirObject extends DirInfo{
    public function __construct(string $dir){
        parent::__construct($dir);
    }

    #@ return array
    # 특정폴더안에 있는 모든 파일 및 폴더명을 넘겨받는다.
    # nothing = array("","gif","html")"포함 시키고 쉽지 않은 폴더 제외 및 파일명 제외"
    public function findFiles(string $pattern='*', Array $nothing=array()) : Array
    {
        $result = array();
        $files= glob($this->dirpath.DIRECTORY_SEPARATOR.$pattern);
        if(is_array($files))
        {
            foreach($files as $filename){
                if (is_file($filename)){
                    $short_filename = basename($filename);
                    if(!in_array($short_filename, $nothing)) $result[] = $short_filename;
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
        $dirs= glob($this->dirpath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
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
