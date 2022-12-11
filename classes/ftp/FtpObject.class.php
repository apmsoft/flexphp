<?php
namespace Flex\Ftp;

class FtpObject
{
    public $conn;

    public function __construct(string $ftp_url){
        if(_FTP_SSL_){
            if(false === ($this->conn = @ftp_ssl_connect($ftp_url, _FTP_PORT_, _FTP_TIME_))){
                exit("ftp ssl connect fail!!!");
            }
        } else {
            if (false === ($this->conn = @ftp_connect($ftp_url, _FTP_PORT_, _FTP_TIME_))) {
                exit("ftp connect fail!!!!");
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