<?php
namespace Flex\Annona\File;

use \SplFileObject;

# 파일을 이용한 스토리지 데이타 관리
class Storage extends SplFileObject
{
    protected $file_name = '';
    private $open_mode;

    public function __construct ( string $file_name, string $mode ){
        $this->file_name = $file_name;
        $this->open_mode = $mode;
        parent::__construct($this->file_name, $this->open_mode);
        if (parent::isFile()) {
            $this->file_name = parent::getRealPath();
        }
    }

    #@ 파일쓰기
    public function write(string $context) : int|bool{
        $written=0;
        if(parent::isWritable()){
            $written = parent::fwrite($context);
        }
    return $written;
    }

    #@ 파일 읽기
    public function read() : string|false{
        $contents = '';
        if(parent::isFile() && parent::isReadable())
        {
            if(PHP_VERSION_ID>=505011){
                $contents = parent::fread(parent::getSize());
            }else{
                while (!parent::eof()) {
                    $contents .=parent::fgets();
                }
            }
        }
    return $contents;
    }

    #@ 파일쓰기
    public function put(string $context) : int|bool{
        $written=0;
        if(parent::isWritable()){
            if(function_exists('file_put_contents')){
                $written = file_put_contents($this->file_name, $context);
            }
        }
    return $written;
    }

    #@ 파일 읽기
    public function get() : string|false{
        $contents = '';
        if(parent::isFile() && parent::isReadable())
        {
            if(function_exists('file_get_contents')){
                $contents = file_get_contents($this->file_name);
            }
        }
    return $contents;
    }

    #@ 쓰기 CSV
    /*
     * $list = array (
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789')
        );
    */
    public function write_csv(array $args) : void {
        if(is_array($args))
        {
            if(PHP_VERSION_ID>=50400){
                foreach ($args as $fields) {
                    parent::fputcsv($fields);
                }
            }else{
                $fp = fopen($this->file_name, $this->open_mode);
                if(is_resource($fp)){
                    foreach ($args as $datav) {
                        fputcsv($fp, $datav);
                    }
                    fclose($fp);
                }
            }
        }
    }

    #@ 읽기 CSV
    public function read_csv() : array{
        $args = [];
        while (!parent::eof()) {
            $args[] = array_filter(parent::fgetcsv());
        }
    return array_filter($args);
    }
}

?>