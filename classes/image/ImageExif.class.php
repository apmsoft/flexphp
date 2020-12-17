<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.0.3
----------------------------------------------------------*/
namespace Fus3\Image;

# purpose : 카메라 촬영 정보
class ImageExif 
{
	private $exifargs = array();

	# computed : 넓이,높이,조리개,촬영거리,CCD
	# ifdo : 카메라정보
	# exif : 노출모드,조리개값,플래시사용여부,화이트발란스,줌,ISO감도,초점거리,측광모드 ,오리지날촬영시간
	# makenote : 펌웨어버전,사용렌즈
	private $setkey_args = array(
		'file'		=> array('FileName','FileSize','FileDateTime','MimeType'),
		'computed'	=> array('Width','Height','ApertureFNumber','FocusDistance','CCDWidth'),
		'ifdo'		=> array('Make','Model','Software','Orientation'),
		'exif'		=> array('ExposureTime','FNumber','Flash','WhiteBalance','DigitalZoomRatio','ISOSpeedRatings','FocalLength','MeteringMode','DateTimeOriginal'),
		'makenote'	=> array('FirmwareVersion','UndefinedTag:0x0095')
	);
	
	# 사진 풀경로
	public function __construct($picture){
		# 로컬 파인인지 체크
		if(!file_exists($picture))
			throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.strval($picture).' not found');

		# 함수 enable 체크
		if(function_exists(exif_read_data)){
			$this->exifargs = @exif_read_data($picture,0,true);
			if($this->exifargs ===false)
				throw new ErrorException(__CLASS__.' :: '.__LINE__.' exif_read_data functions are not available');
		}
	}
	
	# 파일 계산
	public function calcuSize($size)
	{
		$result = '';
		if(!empty($size)){
			$result = sprintf("%0.1f KB", ($size/1024));
			if($r>1024){
				$result = sprintf("%0.1f MB", ($r/1024)); //수점 이하가 0.5 는 1로 반올림한다.
			}
		}
	return $result;
	}
	
	# FILE
	public function getFile()
	{
		$result = array();
		if($this->exifargs['FILE']){
			$args =& $this->exifargs['FILE'];
			foreach($args as $k => $v){
				switch($k){
					case 'FileDateTime': $result[$k] = __date($v,"Y-m-d H:i:s"); break;
					case 'FileSize' : $result[$k] = self::calcuSize($v); break;
					default : $result[$k]= $v;
				}
			}
		}
	return $result;
	}
	
	# COMPUTED
	public function getComputed()
	{
		$result = array();
		if($this->exifargs['COMPUTED']){
			$args =& $this->exifargs['COMPUTED'];
			foreach($args as $k => $v){
				switch($k){
					case 'FocusDistance':
						$result[$k] = $v;
						if(strpos($v,'/') !==false){
							$tmpdistance = explode('/',$v);
							$result[$k] = ($tmpdistance[0]/$tmpdistance[1]).'mm';
						}
						break;
					case 'CCDWidth':
						$result[$k] = (!empty($v)) ? substr($v,0,5).' mm' : '';
						break;
					default :
						$result[$k] = $v;
				}
			}
		}
	return $result;
	}
	
	# IFDO
	public function getIfdo()
	{
		$result = array();
		if($this->exifargs['IFD0']){
			$args =& $this->exifargs['IFD0'];
			foreach($args as $k => $v){
				switch($k){
					case 'Make':
						$result[$k] = str_replace('CORPORATION','',$v);
						break;
					default:
						$result[$k] = $v;	
				}
			}
		}
	return $result;
	}
	
	# EXIF
	public function getExif()
	{
		$result = array();
		if($this->exifargs['EXIF']){
			$args =& $this->exifargs['EXIF'];
			foreach($args as $k => $v){
				switch($k){
					case 'Flash': $result[$k] = ($v==1) ? 'ON' : 'OFF'; break;
					case 'ExposureTime':
						$result[$k] = $v;
						if(strpos($v,'/') !==false){
							$tmpexpo = explode('/',$v);
							$result[$k] = ($tmpexpo[0]/$tmpexpo[0]).'/'.($tmpexpo[1]/$tmpexpo[0]).'s';
						}
						break;
					case 'FocalLength':
						$result[$k] = $v;
						if(strpos($v,'/') !==false){
							$tmpfocal	= explode('/',$v);
							$result[$k] = ($tmpfocal[0]/$tmpfocal[1]).'mm';
						}
						break;
					case 'MakerNote':
						break;
					default: $result[$k] = $v;
				}
			}
		}
	return $result;
	}

	# GPS
	public function getGPS()
	{
		$result = array();
		if($this->exifargs['GPS']){
			$result = $this->exifargs['GPS'];
		}
	return $result;
	}
	
	# MAKENOTE
	public function getMakenote()
	{
		$result = array();
		if($this->exifargs['MAKENOTE']){
			$args =& $this->exifargs['MAKENOTE'];
			foreach($this->setkey_args['makenote'] as $k => $v){
				$result[$k] = $v;
			}
		}
	return $result;
	}
	
	# 한번에 추출하기
	public function getAvailable(){
		$args = array();
		if(count($this->exifargs)>0){
			foreach($this->setkey_args as $k => $v){
				$methodName = 'get'.ucwords($k);
				$args += call_user_func_array(array(&$this, $methodName), array());
			}
		}
	return $args;
	}
}
?>
