<?php
namespace Flex\Util;

use Flex\R\R;
use Flex\Image\ImageExif;
use Flex\Dir\DirInfo;
use Flex\Cipher\CipherEncrypt;

class UtilFileUpload extends ImageExif
{	
	# 업로드 경로
	public $upload_dir;
	public $upload_date_dir;
	private $config = array();
	private $ext_args = array();
	private $upfilemaxsize = 1048576; // 1M (1024 * 1024)
	private $_files = array('filename'=>'','mediaType'=>'','size'=>0,'error'=>0);

	#@ void /=================================================================
	# 파일 업로드 기본 초기화 작업
	public function __construct(array $config, array $fileinfo)
	{
		$this->config = $config;
		// print_r($config);
		
		# 추가 파일명
		$this->upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/'.$this->config['extract_id'];

		# 업로드된 파일 정보
		$this->_files = $fileinfo;
	}
	
	#@ return array
	# 파일 올리기
	public function filter() : string
	{
		# 게시판 환경 설정 데이타 확인
		$file_extention = $this->config['file_extension'];
		$file_max_size = $this->config['file_maxsize'];

		# 기본 셋팅
		$this->ext_args = explode(',',$file_extention); #파일 확장자 등록
		$this->setMaxFilesize($file_max_size); #파일 사이즈(Mbyte) 등록 및 체크

		# 파일 확장자 체크
		if(!$this->isFileExtention()){
			$this->error_report('e_extension_not_allowed', R::$sysmsg['e_extension_not_allowed']);
		}

		# 파일 사이즈 체크
		if(!$this->isMaxFilesize()){
			$this->error_report('e_upload_max_filesize', R::$sysmsg['e_upload_max_filesize']);
		}

		# 업로드 폴더 체크 및 생성
		$dirObj = new DirInfo($this->upload_dir);
		$dirObj->makesDir();

		#저장할파일명
		$fileplusname = str_replace(array('.',' '),array('_','_'),microtime());
		$sfilename = $this->config['extract_id'].'_'.$fileplusname;
		$cipherEncrypt = new CipherEncrypt($sfilename);
		$sfilename = $cipherEncrypt->_md5_utf8encode().'.'.$this->getExtName();

	return $sfilename;
	}

	public function chkOrientation(){
		// exif
		if( preg_match('/(jpeg|jpg)/',$this->getExtName()) )
		{
			parent::__construct($this->upload_dir.DIRECTORY_SEPARATOR.$sfilename);
			$ifdo = $this->getIfdo();
			// print_r($ifdo);
			if(isset($ifdo['Orientation']) && !empty($ifdo['Orientation'])){
				$file = imagecreatefromjpeg($this->upload_dir.DIRECTORY_SEPARATOR.$sfilename);
				switch($ifdo['Orientation']) {
                    case 8:
                        $rotate = imagerotate($file,90,0);
                        imagejpeg($rotate,$this->upload_dir.DIRECTORY_SEPARATOR.$sfilename);
                        break;
                    case 3:
                        $rotate = imagerotate($file,180,0);
                        imagejpeg($rotate,$this->upload_dir.DIRECTORY_SEPARATOR.$sfilename);
                        break;
                    case 6:
                        $rotate = imagerotate($file,-90,0);
                        imagejpeg($rotate,$this->upload_dir.DIRECTORY_SEPARATOR.$sfilename);
                        break;
                    }
			}
		}
	}

	#@ return String
	# 파일 확장자 추출
	public function getExtName() : string{
		$tmpfile = basename($this->_files['filename']);
		$count= strrpos($tmpfile,'.');
		$file_extension= strtolower(substr($tmpfile, $count+1));
	return $file_extension;
	}

	#@ void
	# 최대 업로드 전송 허용 사이즈
	# 8(M),12(M),100(M)
	// public function setMaxFilesize(string|int $maxsize):void{
	public function setMaxFilesize($maxsize) : void{
		$this->upfilemaxsize = (int)(1024 * 1024 * $maxsize);
	}

	#@ return boolean
	// 허용된 업로드 파일인지 체크
	public function isFileExtention() : bool{
		$result = false;
		$exe = $this->getExtName();
		if(in_array($exe,$this->ext_args)){
			$result = true;
		}
	return $result;
	}

	#@ return boolean
	# 설정한 용량값을 넘었는지 체크
	public function isMaxFilesize() : bool{
		if($this->_files['size'] >= $this->upfilemaxsize) return false;
	return true;
	}

	# string
	# 첨부 실파일명 특수문자 제거
	public function cleansEtcWords($ofilename){
		if($ofilename ==''){
			return '';
		}
		$ofilename = preg_replace("/[ #\&\+\-%@=\/\\\:;,\'\"\^`~\|\!\?\*$#<>()\[\]\{\}]/i",'_',$ofilename); 
		$ofilename = preg_replace('/\s\s+/', '_', $ofilename); // 연속된 공백을 하나의 문자로 변경
	return $ofilename;
	}

	# void
	public function fileRemove($filename){
		@unlink($filename);
	}

	public function error_report($msg_code, $msg){
		throw new \Exception(strval(json_encode(['result'=>'false','msg_code'=>$msg_code,'msg'=>$msg])));
	}
}
?>
