<?php
namespace Flex\Ftp;

final class Ftp extends FtpObject
{
    private $ascii_type = array(
        'txt','htm','html','phtml','php','php3','php4',
        'inc','ini','asp','aspx','jsp','css','js'
    );

    public function __construct(string $ftp_url='',string $ftp_user='', string $ftp_passwd=''){
        $_ftp_host   = ($ftp_url)   ? $ftp_url      : _FTP_HOST_;
        $_ftp_user   = ($ftp_user)  ? $ftp_user     : _FTP_USER_;
        $_ftp_passwd = ($ftp_passwd)? $ftp_passwd   : _FTP_PASSWD_;

        parent::__construct($_ftp_host);
        $this->ftp_login($_ftp_user, $_ftp_passwd);
    }

    # 파일 내용 읽어 오기
    public function open_file_read(string $tmpfile, string $remote_file) : string
    {
        if(!$this->ftp_get($tmpfile, $remote_file, self::chk_open_mode($remote_file)))
            return false;

        $fp=fopen($tmpfile,'r');
        $contents = fread($fp, filesize($tmpfile));
        fclose($fp);

    return $contents;
    }

    public function open_file_write(string $tmpfile, string $remote_file, string $contents) : bool
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

    # 파일삭제
    public function delete_file(string $dir, string $del_filename) : void{
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

    # 로컬 파일인지 체크
    private function isExists(string $filename) : bool{
        if(!file_exists($filename)) return false;
    return true;
    }

    #@ return int
    private function chk_open_mode(string $filename) : int
    {
        $extention = strtolower(self::getExtention($filename));

        if(!in_array($extention, $this->ascii_type)) return FTP_ASCII;
        else return FTP_BINARY;
    }

    # 파일 확장자 추출
    private function getExtention(string $filename) : string{
        $tmpfile = basename($filename);
        $count= strrpos($tmpfile,'.');
        $extention= strtolower(substr($tmpfile, $count+1));
    return $extention;
    }
}
?>