<?php
/** ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : http://www.apmsoftax.com
| @Editor   : Eclipse(default)
| @version  : 0.9
----------------------------------------------------------*/
namespace Fus3\Ftp;

class FtpObject
{
    public $conn;

    public function __construct($ftp_url){
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

    public function __call($func,$params){
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