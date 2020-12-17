<?php
namespace Flex\Util;

class UtilResFtpHelper {
    public $filename;

    public $local_filename;
    public $local_real_filename;
    public $local_real_dir;

    public $server_filename;
    public $save_server_filename;

    public function __construct($dir,$filename)
    {
        $this->filename = $filename.'.json';

        # 국가 파일 있는지 체크 후 사용할 파일 결정
        $nation_filename = _FTP_DIR_.$dir.DIRECTORY_SEPARATOR.$filename.'_'._LANG_.'.json';
        if(file_exists($nation_filename)){
            $this->filename = $filename.'_'._LANG_.'.json';
        }

        # local filename
        $this->local_real_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR.$dir;

        # local filename
        $this->local_filename = _ROOT_PATH_.DIRECTORY_SEPARATOR._DATA_.DIRECTORY_SEPARATOR.$this->filename;

        # local real filename
        $this->local_real_filename = $this->local_real_dir.DIRECTORY_SEPARATOR.$this->filename;

        # server filename
        $this->server_filename = _FTP_DIR_.$dir.DIRECTORY_SEPARATOR.$this->filename;
    }

    public function set_save_server_filename($dir){
        # server filename
        $this->save_server_filename = _FTP_DIR_.$dir.DIRECTORY_SEPARATOR.$this->filename;
    }

    # 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get($propertyName){
        $result = '';
		if(property_exists(__CLASS__,$propertyName)){
			$result = $this->{$propertyName};
        }
    return $result;
	}
}
?>