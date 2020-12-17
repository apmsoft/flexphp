<?php
/** ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : http://www.apmsoftax.com
| @Editor   : Eclipse(default)
| @version  : 0.9
----------------------------------------------------------*/
namespace Fus3\Ftp;

final class Ftp extends FtpObject
{
    private $ascii_type = array(
        'txt','htm','html','phtml','php','php3','php4',
        'inc','ini','asp','aspx','jsp','css','js'
    );

    public function __construct($ftp_url='',$ftp_user='', $ftp_passwd=''){
        $_ftp_host   = ($ftp_url)   ? $ftp_url      : _FTP_HOST_;
        $_ftp_user   = ($ftp_user)  ? $ftp_user     : _FTP_USER_;
        $_ftp_passwd = ($ftp_passwd)? $ftp_passwd   : _FTP_PASSWD_;

        parent::__construct($_ftp_host);
        $this->ftp_login($_ftp_user, $_ftp_passwd);
    }

    #@ return boolean | string
    # 파일 내용 읽어 오기
    public function open_file_read($tmpfile, $remote_file)
    {
        if(!$this->ftp_get($tmpfile, $remote_file, self::chk_open_mode($remote_file)))
            return false;

        $fp=fopen($tmpfile,'r');
        $contents = fread($fp, filesize($tmpfile));
        fclose($fp);

    return $contents;
    }

    #@ return boolean
    #
    public function open_file_write($tmpfile, $remote_file, $contents)
    {
        if(!self::isExists($tmpfile)){
            return false;
        }

        if(empty($contents))
            return false;

        $fp = fopen($tmpfile, 'w');
        fwrite($fp, $contents);
        fclose($fp);
        if(!$this->ftp_put($remote_file, $tmpfile, self::chk_open_mode($remote_file)))
            return false;

        @unlink($tmpfile);
    return true;
    }

    #@ void
    # 파일삭제
    public function delete_file($dir, $del_filename){
        $files = $this->ftp_nlist($dir);
        if(is_array($files)){
            foreach ($files as $file){
                $realname = basename($file);
                if($del_filename == $realname){
                    $this->ftp_delete($file);
                    break;
                }
            }
        }
    }

    #@ return boolean
    # 로컬 파일인지 체크
    private function isExists($filename){
        if(!file_exists($filename)) return false;
    return true;
    }

    #@ return int
    private function chk_open_mode($filename)
    {
        $extention = strtolower(self::getExtention($filename));

        if(!in_array($extention, $this->ascii_type)) return FTP_ASCII;
        else return FTP_BINARY;
    }

    #@ return String
    # 파일 확장자 추출
    private function getExtention($filename){
        $tmpfile = basename($filename);
        $count= strrpos($tmpfile,'.');
        $extention= strtolower(substr($tmpfile, $count+1));
    return $extention;
    }
}
?>