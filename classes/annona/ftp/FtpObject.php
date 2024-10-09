<?php
namespace Flex\Annona\Ftp;

class FtpObject
{
    public const __version = '1.1';
    public $conn;

    public function __construct(string $ftp_url, int $port, bool $is_ssl, int $time){
        if($is_ssl){
            if(false === ($this->conn = @ftp_ssl_connect($ftp_url, $port, $time))){
                throw new \Exception("ftp ssl connect fail!!!!");
            }
        } else {
            if (false === ($this->conn = @ftp_connect($ftp_url, $port, $time))) {
                throw new \Exception("ftp connect fail!!!!");
            }
        }
    }

    public function __call(string $func,array $params){
        if(strstr($func,'ftp_') !== false && function_exists($func)){
            array_unshift($params,$this->conn);
            return call_user_func_array($func,$params);
        }
    }

    public function __destruct(){
        ftp_close($this->conn);
    }
}
?>