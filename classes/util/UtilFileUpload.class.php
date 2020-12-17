<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 1.7.1
----------------------------------------------------------*/
namespace Flex\Util;

use Flex\Image\ImageExif;
use Flex\Files\FilesUpload;
use Flex\Dir\DirInfo;
use Flex\Cipher\CipherEncrypt;

class UtilFileUpload extends ImageExif
{	
	# 업로드 경로
	private $upload_dirname;
	private $upload_date_dir;
	private $config = array();

	#@ void /=================================================================
	# 파일 업로드 기본 초기화 작업
	public function __construct($config)
	{
		$this->config = $config;
		
		# 추가 파일명
		$this->upload_date_dir = date('Y/m',time());
		$this->upload_dirname=_ROOT_PATH_.DIRECTORY_SEPARATOR._UPLOAD_.DIRECTORY_SEPARATOR.$this->upload_date_dir;
	}
	
	#@ return array
	# 파일 올리기
	public function fileUpload($tmp_name, $name, $size, $type, $error)
	{
		if(!$tmp_name){Out::prints_json(array('result'=>'false','msg_code'=>'e_no_was_uploaded','msg'=>R::$sysmsg['e_no_was_uploaded']));}

		$fileObj = null;
		try{
			$fileObj = new FilesUpload($tmp_name, $name, $size, $error);
		}catch(Exception $e){
			Out::prints_json(array('result'=>'false','msg_code'=>'e_no_was_uploaded','msg'=>$e->getMessage()));
		}

		# 게시판 환경 설정 데이타 확인
		$file_extention = $this->config['file_extension'];
		$file_max_size = $this->config['file_maxsize'];

		# 기본 셋팅
		$fileObj->setFileExtention(str_replace(',','|',$file_extention));	#파일 확장자 등록
		$fileObj->setMaxFilesize($file_max_size); #파일 사이즈(Mbyte) 등록 및 체크

		# 파일 확장자 체크
		if(!$fileObj->isFileExtention()){
			Out::prints_json(array('result'=>'false','msg_code'=>'e_extension_not_allowed','msg'=>R::$sysmsg['e_extension_not_allowed']));
		}

		# 파일 사이즈 체크
		if(!$fileObj->isMaxFilesize()){
			Out::prints_json(array('result'=>'false','msg_code'=>'e_upload_max_filesize','msg'=>R::$sysmsg['e_upload_max_filesize']));
		}

		# 업로드 폴더 체크 및 생성
		$dirObj = new DirInfo($this->upload_dirname);
		$dirObj->makesDir();

		#원본 파일 복사
		$fileplusname = str_replace(array('.',' '),array('_','_'),microtime());
		$sfilename = str_replace('/','_',$this->upload_date_dir).'_'.$fileplusname;
		$cipherEncrypt = new CipherEncrypt($sfilename);
		$sfilename = $cipherEncrypt->_md5_utf8encode().'.'.$fileObj->getExtName();
		if(!$fileObj->move_upload_files($tmp_name, $this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename)){
			Out::prints_json(array('result'=>'false','msg_code'=>'copy_failed','msg'=>'copy is failed'));
		}

		// exif
		if( preg_match('/(jpeg|jpg)/',$fileObj->getExtName()) ){
			parent::__construct($this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename);
			$ifdo = $this->getIfdo();
			// print_r($ifdo);
			if(isset($ifdo['Orientation']) && !empty($ifdo['Orientation'])){
				$file = imagecreatefromjpeg($this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename);
				switch($ifdo['Orientation']) {
			        case 8:
			            $rotate = imagerotate($file,90,0);
			            imagejpeg($rotate,$this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename);
			            break;
			        case 3:
			            $rotate = imagerotate($file,180,0);
			            imagejpeg($rotate,$this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename);
			            break;
			        case 6:
			            $rotate = imagerotate($file,-90,0);
			            imagejpeg($rotate,$this->upload_dirname.DIRECTORY_SEPARATOR.$sfilename);
			            break;
			    }
			}
		}

		# result
		$file_args=array(
			'sfilename'=>$sfilename,
			'file_type'=>(preg_match('/(gif|jpeg|jpg|png)/',$fileObj->getExtName()))?'image/'.$fileObj->getExtName():'application/'.$fileObj->getExtName(),
			'ofilename'=>$this->cleansEtcWords($name),
			'file_size'=>$size,
			'directory'=>DIRECTORY_SEPARATOR._UPLOAD_.DIRECTORY_SEPARATOR.$this->upload_date_dir
		);

	return $file_args;
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
}
?>
