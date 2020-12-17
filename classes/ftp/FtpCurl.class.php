<?php
namespace Fus3\Ftp;

class FtpCurl
{
 
    private $ftp_host = ""; //ftp 서버 호스트 주소
    private $ftp_port = 21; //ftp 서버 호스트 포트
    private $ftp_userid = ""; //ftp 서버 접속 계정아이디
    private $ftp_userpass = ""; //ftp 서버 접속 계정비밀번호
    private $is_debug_output = false;
 
    private $ftp_url = null;
    private $ftp_userpwd = null;
 
    public function __construct($host, $port = 21, $userid = "", $userpass = "") {
 
        $this->ftp_host = $host;
        $this->ftp_port = $port;
        $this->ftp_userid = $userid;
        $this->ftp_userpass = $userpass;
 
        $this->ftp_url = "ftp://{$this->ftp_host}:{$this->ftp_port}";
        if($this->ftp_userid) {
            $this->ftp_userpwd = "{$this->ftp_userid}:{$this->ftp_userpass}";
        }
    }
 
    public function __destruct() {
 
    }
 
    public function setDebug($debug) {
        $this->is_debug_output = !!$debug;
    }
 
    /**
     * ftp 파일목록을 조회한다.
     * @param $dir 디렉토리 경로, 루트폴더부터의 경로이며, 루트로 지정하는 경우는 파라메타를 공백으로 넘김
     * @return 기본 경로에 있는 파일 목록
     */
    function getList($dir) {
 
        $result = array();
 
        $ftp_url =  $this->ftp_url;
        if($dir) $ftp_url.= "$dir";
 
        $curl = curl_init();
        try {
            curl_setopt($curl, CURLOPT_URL, $ftp_url);
            curl_setopt($curl, CURLOPT_USERPWD, $this->ftp_userpwd);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FTPLISTONLY,true);
 
            $ftp_result = curl_exec($curl);
            if(curl_errno($curl) > 0) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
            $file_list = explode("\n", $ftp_result);
 
            foreach($file_list as $filename) {
                if(trim($filename)) {
                    $result[] = trim($filename);
                }
            }
 
            curl_close($curl);
        } catch(Exception $e) {
            $this->debugOutput($e);
        }
 
        return $result;
    }
 
    /**
     * ftp 파일 내용을 읽어서 반환한다.
     * @param $filepathname  ftp 서버내 파일 full 경로(root 부터 시작)
     * @return 파일내용, 읽기가 실패하면 null을 리턴.
     */
    public function getFtpFileContent($filepathname) {
 
        $ftp_url =  $this->ftp_url.$filepathname;
 
        $curl = curl_init();
 
        try {
 
            curl_setopt($curl, CURLOPT_URL, $ftp_url);
            curl_setopt($curl, CURLOPT_USERPWD, $this->ftp_userpwd);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt( $curl, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 ); //용량이 큰 파일은 timeout을 늘려준다.
 
            $ftp_result = curl_exec($curl);
            if(curl_errno($curl) > 0) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
 
            curl_close($curl);
 
            return $ftp_result;
        } catch(Exception $e) {
            $this->debugOutput($e);
        }
 
        return null;
    }
 
    /**
     * ftp 에서 지정된 파일을 다운로드한다.
     * @param $filename 다운로드할 파일
     * @param $download_filepath 다운로드후 저장할 파일 경로
     * @return bool 다운로드 성공 : true, 다운로드 실패 : false
     */
    public function downloadFile($filepathname, $download_filepath) {
 
        $ftp_url =  $this->ftp_url.$filepathname;
 
        $curl = curl_init();
 
        try {
            $download_dir = dirname($download_filepath);
 
            if(!file_exists($download_dir)) {
                throw new Exception("file directory not found!!");
            }
 
            $file = fopen($download_filepath, "w");
            curl_setopt($curl, CURLOPT_URL, $ftp_url);
            curl_setopt($curl, CURLOPT_USERPWD, $this->ftp_userpwd);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
            curl_setopt( $curl, CURLOPT_BINARYTRANSFER, true );
            curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 ); //용량이 큰 파일은 timeout을 늘려준다.
            curl_setopt( $curl, CURLOPT_FILE, $file );
 
            curl_exec ($curl);
 
            fclose($file);
 
            if(curl_errno($curl) > 0) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
 
            curl_close($curl);
            return true;
        } catch(Exception $e) {
            $this->debugOutput($e);
        }
 
        return false;
    }
 
    /**
     * ftp 서버내 파일을 삭제한다.
     * 디렉토리는 삭제되지 않는다.
     *
     * @param $filepathname ftp 서버내 파일 full 경로(root 부터 시작)
     * @return bool
     */
    function deleteFile($filepathname) {
 
        $curl = curl_init();
 
        try {
            curl_setopt($curl, CURLOPT_URL, $this->ftp_url);
            curl_setopt($curl, CURLOPT_USERPWD, $this->ftp_userpwd);
            curl_setopt($curl, CURLOPT_POSTQUOTE, array("dele $filepathname"));
            curl_exec ($curl);
 
            if(curl_errno($curl) > 0) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
 
            curl_close($curl);
            return true;
        } catch(Exception $e) {
            $this->debugOutput($e);
        }
 
        return false;
    }
 
 
 
    /**
     * ftp 파일 내용을 읽어서 반환한다.
     * curl을 사용하지 않는 방식
     *
     * @param $filepathaname  ftp 서버내 파일 full 경로(root 부터 시작)
     * @return null|string
     */
    function getURLFileContent($filepathaname) {
 
        $ftp_url = "ftp://{$this->ftp_userid}:{$this->ftp_userpass}@{$this->ftp_host}:{$this->ftp_port}{$filepathaname}";
 
        echo $ftp_url."\n";
 
        try {
            $contents = file_get_contents($ftp_url);
 
            /* 아래와 같이 파일스트림으로도 처리가능
            $handle = fopen($ftp_url, "r");
            $contents = "";
            while ($line = fread($handle, 1024)) {
                $contents .= $line;
            }
            fclose($handle);
            */
            return $contents;
        } catch(Exception $e) {
            $this->debugOutput($e);
        }
 
        return null;
    }
 
    /**
     * ftp 서버내 디렉토리를 삭제한다.
     * @param $pathname
     */
    function deleteDirectory($pathname) {
        //미구현
    }
 
    /**
     * ftp 서버내 디렉토리를 생성한다.
     * @param $pathname
     */
    function createDirectory($pathname) {
        //미구현
    }
 
    /**
     * ftp 서버내 파일을 특정 디렉토리로 복사한다.
     * @param $filepathname
     * @param $target_directory
     */
    function copy($filepathname, $target_directory) {
        //미구현
    }
 
    /**
     * ftp 서버내 파일을 특정 디렉토리로 이동한다.
     * @param $filepathname
     * @param $target_directory
     */
    function move($filepathname, $target_directory) {
        //미구현
    }
 
    private function debugOutput($e) {
        if($this->is_debug_output) {
            echo "ftp_url : ".$this->ftp_url."\n";
            echo "ftp_userpwd : ".$this->ftp_userpwd."\n";
            echo "errorCode : ".$e->getCode().", errorMessage : ".$e->getMessage()."\n";
        }
 
    }
 
}
?>