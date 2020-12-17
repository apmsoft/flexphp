<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 1.3.2
----------------------------------------------------------*/
namespace Fus3\Files;

use Fus3\R\R;

# parnet : Files
# purpose : 파일 전송 값 처리를 목적으로 함
class FilesUpload
{
	private $ext_args = array();
	private $upfilemaxsize = 1048576; // 1M (1024 * 1024)
	private $_files = array('tmpfile'=>'','filename'=>'','size'=>0);
	private $_upload_error_number = 0;

	# 정상적인 업로드 파일인지 체크
	# _FILES['파일명']['tmp_name']
	public function __construct($tmpfile, $filename, $upfiles_size, $errornum){
		if(!empty($filename))
		{
			if($errornum > 0) 
				throw new ErrorException(__LINE__.' : ('.$errornum.')'. self::getUpfileErrorMsg());
			else{
				// 파일업로드 확인
				if(!self::is_upload_files($tmpfile)) 
					throw new ErrorException(__LINE__.' : '. self::getUpfileErrorMsg());
				else{
					# 업로드 파일정보
					$this->_files = array(
						'tmpfile'  =>$tmpfile,
						'filename' =>$filename,
						'size'     =>$upfiles_size
					);
				}
			}
		}
	}

	#@ void
	# 전송이 허용된 파일 확장자 등록
	# 방법1 : gif,jpeg,txt,png
	# 방법2 : gif|jpeg|txt|png
	# 방법3 : gif
	public function setFileExtention($ext){
		if(!empty($ext)){
			$ext = str_replace('|',',',$ext);
			if(strpos($ext, ',') !==false){
				$tmpargs = explode(',',$ext);
				$this->ext_args = array_merge($this->ext_args,$tmpargs);
			}else{
				$this->ext_args[] = $ext;
			}
		}
	}

	#@ void
	# 최대 업로드 전송 허용 사이즈
	# 8(M),12(M),100(M)
	public function setMaxFilesize($maxsize){
		$this->upfilemaxsize = (int)(1024 * 1024 * $maxsize);
	}

	#@ return boolean
	// 허용된 업로드 파일인지 체크
	public function isFileExtention(){
		$ext = implode('|',$this->ext_args);
		if(!preg_match("/(?:{$ext})$/i", basename($this->_files['filename']))) return false;
	return true;
	}

	#@ return boolean
	# 설정한 용량값을 넘어쓴지 체크
	public function isMaxFilesize(){
		if($this->_files['size'] >= $this->upfilemaxsize) return false;
	return true;
	}

	#@ return boolean
	# 업로드된 파일인지 체크
	private function is_upload_files($tmpfile){
		if(!is_uploaded_file($tmpfile)) return false;
	return true;
	}

	#@ return String boolean
	# 업로드 파일 복사하기
	public function move_upload_files($tmpfile, $sfilename){
		if(!move_uploaded_file($tmpfile, $sfilename)) return false;
	return $sfilename;
	}

	#@ return String boolean
	# 파일 복사하기
	public function copy_upload_files($savefilename){
		$sfilename = false;
		if(is_array($this->_files))
		{
			if(!self::isFileExtention())
				throw new ErrorException(R::$sysmsg['e_extension_not_allowed']);

			// 허용용량(max)인지 체크
			if(self::isMaxFilesize())
				throw new ErrorException(R::$sysmsg['e_upload_max_filesize']);

			// 복사하기
			if( ($sfilename = self::move_upload_files($this->_files['tmpfile'],$savefilename)) !== false)
				return $sfilename;
		}
	return $sfilename;
	}

	#@ return String
	# 파일 확장자 추출
	public function getExtName(){
		$tmpfile = basename($this->_files['filename']);
		$count= strrpos($tmpfile,'.');
		$file_extension= strtolower(substr($tmpfile, $count+1));
	return $file_extension;
	}

	#@ return String
	# 에러메세지 가져오기
	public function getUpfileErrorMsg(){
		$msg = '';
		switch($this->_upload_error_number){
			case 1:
			case 2:$msg=R::$sysmsg['e_upload_max_filesize']; break;
			case 3:$msg=R::$sysmsg['e_partially_uploaded']; break;
			case 4:$msg=R::$sysmsg['e_no_was_uploaded']; break;
			case 6:$msg=R::$sysmsg['e_miss_temp_folder']; break;
			case 7:$msg=R::$sysmsg['e_failed_write_disk']; break;
			case 8:$msg=R::$sysmsg['e_upload_stopped']; break;
			default : $msg=R::$sysmsg['e_not_uploaded_file'];
		}
	return $msg;
	}
}
?>
