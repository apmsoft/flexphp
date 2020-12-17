<?php
namespace Fus3\Preference;

use \SplFileObject;

# 파일을 이용한 스토리지 데이타 관리
class PreferenceInternalStorage extends SplFileObject
{
    protected $file_name = '';
    private $open_mode;

    public function __construct ( $file_name, $mode ){
        $this->file_name = $file_name;
        $this->open_mode = $mode;
        parent::__construct($this->file_name, $this->open_mode);
        if (parent::isFile()) {
            $this->file_name = parent::getRealPath();
        }
    }

    #@ return int
    public function writeInternalStorage($context){
        $written=0;
        if(parent::isWritable()){
            $written = parent::fwrite($context);
        }
    return $written;
    }

    #@ return String
    public function readInternalStorage(){
        $contents='';
        if(parent::isFile() && parent::isReadable()){
            if(PHP_VERSION_ID>=505011){
                $contents = parent::fread(parent::getSize());
            }else{
                while (!parent::eof()) {
                    $contents.=parent::fgets();
                }
            }
        }
    return $contents;
    }

    #@ void
    /*
     * $list = array (
            array('aaa', 'bbb', 'ccc', 'dddd'),
            array('123', '456', '789')
        );
    */
    public function writeInternalStorageCSV($args){
        if(is_array($args)){
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

    #@ array
    public function readInternalStorageCSV(){
        $args = array();
        while (!parent::eof()) {
            $args[] = parent::fgetcsv();
        }
    return $args;
    }
}

?>