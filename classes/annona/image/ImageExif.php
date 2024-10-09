<?php
namespace Flex\Annona\Image;

# purpose : 카메라 촬영 정보
class ImageExif 
{
	public const __version = '0.9';
	private $exifargs = [];

	# computed : 넓이,높이,조리개,촬영거리,CCD
	# ifdo : 카메라정보
	# exif : 노출모드,조리개값,플래시사용여부,화이트발란스,줌,ISO감도,초점거리,측광모드 ,오리지날촬영시간
	# makenote : 펌웨어버전,사용렌즈
	private $setkey_args = [
		'file'		=> ['FileName','FileSize','FileDateTime','MimeType'],
		'computed'	=> ['Width','Height','ApertureFNumber','FocusDistance','CCDWidth'],
		'ifdo'		=> ['Make','Model','Software','Orientation'],
		'exif'		=> ['ExposureTime','FNumber','Flash','WhiteBalance','DigitalZoomRatio','ISOSpeedRatings','FocalLength','MeteringMode','DateTimeOriginal'],
		'makenote'	=> ['FirmwareVersion','UndefinedTag:0x0095']
	];
	
	# 사진 전체 경로
	public function __construct(string $picture){
		# 로컬 파인인지 체크
		if(!file_exists($picture))
			throw new \Exception(__CLASS__.' :: '.__LINE__.' '.strval($picture).' not found');

		# 함수 enable 체크
		if(function_exists('exif_read_data')){
			$this->exifargs = @exif_read_data($picture,0,true);
			if($this->exifargs ===false)
				throw new \Exception(__CLASS__.' :: '.__LINE__.' exif_read_data functions are not available');
		}
	}
	
	# FILE
	public function getFile() : array
	{
		$result = [];
		if(isset($this->exifargs['FILE'])){
			$result = $this->exifargs['FILE'];
		}
	return $result;
	}
	
	# COMPUTED
	public function getComputed() : array
	{
		$result = [];
		if(isset($this->exifargs['COMPUTED'])){
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
	public function getIfdo() : array
	{
		$result = [];
		if(isset($this->exifargs['IFD0'])){
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
	public function getExif() : array
	{
		$result = [];
		if(isset($this->exifargs['EXIF'])){
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
	public function getGPS() : array
	{
		$result = [];
		if(isset($this->exifargs['GPS'])){
			$result = $this->exifargs['GPS'];
		}
	return $result;
	}
	
	# MAKENOTE
	public function getMakenote() : array
	{
		$result = [];
		if(isset($this->exifargs['MAKENOTE'])){
			$args =& $this->exifargs['MAKENOTE'];
			foreach($this->setkey_args['makenote'] as $k => $v){
				$result[$k] = $v;
			}
		}
	return $result;
	}
	
	# 한번에 추출하기
	public function fetch() : array
	{
		$args = [];
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
