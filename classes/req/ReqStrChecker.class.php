<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor   : VSCode
| @UPDATE   : 1.1.1
----------------------------------------------------------*/
namespace Fus3\Req;

# purpose : 문자를 체크(Ascii 문자 코드를 활용하여) 한다 / preg,ereg 정규식 보다 훨 빠름
class ReqStrChecker{
	private $str;
	private $len = 0;

	public function __construct($s){
		$this->str = trim($s);
		if(!self::isNull()){
			$this->len = strlen($s);
		}
	}

	#@ return boolean
	# null 값인지 체크한다 [ 널값이면 : true / 아니면 : false ]
	public function isNull(){
		$result = false;

		if(is_null($this->str) || $this->str==''){
			$result = true;
		}
	return $result;
	}

	#@ return boolean
	# 문자와 문자사이 공백이 있는지 체크 [ 공백 있으면 : false / 없으면 : true ]
	public function isSpace(){
		$result = true;
		$str_split	= count(preg_split("/ /", $this->str)); //split("[[:space:]]+",$this->str);
		$count = (is_array($str_split)) ? count($str_split) : 0;
		for($i=0; $i<$count; $i++){
			if($i>0){
				$result = false;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 연속적으로 똑같은 문자는 입력할 수 없다  [ 반복문자 max 이상이면 : false / 아니면 : true ]
	# ex : 010-111-1111,010-222-1111 형태제한
	# max = 3; // 반복문자 3개 "초과" 입력제한
	public function isSameRepeatString($max=3){
		$result = true;
		$sameCount = 0;
		$preAsciiNumber = 0;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if( ($preAsciiNumber == $asciiNumber) && ($preAsciiNumber>0) ) $sameCount += 1;
			else $preAsciiNumber = $asciiNumber;

			if($sameCount==$max){
				$result = false;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 숫자인지 체크 [ 숫자면 : true / 아니면 : false ]
	# Ascii table = 48 ~ 57
	public function isNumber(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<47 || $asciiNumber>57){
				$result = false;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 영문인지 체크 [ 영문이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90], 소문자[97~122]
	public function isAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
			else{ $result = false; }
		}
	return $result;
	}

	#@ return boolean
	# 영문이 대문자 인지체크 [ 대문자이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90]
	public function isUpAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<65 || $asciiNumber>90){
				$result = false;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 영문이 소문자 인지체크 [ 소문자면 : true / 아니면 : false ]
	# Ascii table = 소문자[97~122]
	public function isLowAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<97 || $asciiNumber>122){
				$result = false;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 한글인지 체크한다 [ 한글이면 : true / 아니면 : false ]
	# Ascii table = 128 >
	public function isKorean(){
		$result = false;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber>128){
				$result = true;
				break;
			}
		}
	return $result;
	}

	#@ return boolean
	# 특수문자 입력여부 체크 [ 특수문자 찾으면 : false / 못찾으면 : true ]
	# allow = "-,_"; 허용시킬
	# space 공백은 자동 제외
	public function isEtcString($allow)
	{
		# 허용된 특수문자 키
		$allowArgs = array();
		if(!empty($allow))
		{
			$tmpArgs =  (strpos($allow,',') !==false ) ? explode(',',$allow) : $tmpArgs=array($allow);
			if(is_array($tmpArgs)){
				foreach($tmpArgs as $k => $v){
					$knumber = Ord($v);
					$allowArgs['s'.$knumber] = $v;
				}
			}
		}

		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(array_key_exists('s'.$asciiNumber, $allowArgs) === false){
				if( ($asciiNumber<48) && ($asciiNumber != 46 && $asciiNumber != 32) ){ $result = false; break; }
				else if($asciiNumber>57 && $asciiNumber<65){ $result = false; break; }
				else if($asciiNumber>90 && $asciiNumber<97){ $result = false; break; }
				else if($asciiNumber>122 && $asciiNumber<128){ $result = false; break; }
			}
		}
	return $result;
	}

	#@ return boolean
	# 첫번째 문자가 영문인지 체크한다[ 찾으면 : true / 못찾으면 : false ]
	public function isFirstAlphabet(){
		$result = true;
		$asciiNumber = Ord($this->str[0]);
		if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
		else{ $result = false; }
	return $result;
	}

	#@ return boolean
	# 문자길이 체크 한글/영문/숫자/특수문자/공백 전부포함
	# min : 최소길이 / max : 최대길이 utf-8
	public function isStringLength($min,$max){
		$strCount = 0;
		for($i=0;$i<$this->len;$i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<=127 && $asciiNumber>=0){ $strCount++; }
			else if($asciiNumber<=223 && $asciiNumber>=194){ $strCount++; $i+1; }
			else if($asciiNumber<=239 && $asciiNumber>=224){ $strCount++; $i+2; }
			else if($asciiNumber<=244 && $asciiNumber>=240){ $strCount++; $i+3; }
		}

		if($strCount<$min) return false;
		else if($strCount>$max) return false;
		else return true;
	}

	#@ return boolean
	# 날짜가 정확한 날짜인지 체크
	# 날짜 데이타 타입 (2012-01-12)
	public function chkDate(){
		if(strpos($this->str,'-') ===false){
			return false;
		}
		$ymd_args = explode('-',$this->str);
		if(is_array($ymd_args)){
			if(!checkdate($ymd_args[1],$ymd_args[2],$ymd_args[0])){
				return false;
			}
		}
	return true;
	}

	#@ return boolean
	# 시간이 정확한 시간에 속하는치 형태인지 체크
	# 시간 데이터 타입(13:59:59)
	public function chkTime(){
		if(strpos($this->str,':') ===false){
			return false;
		}
		$pattern1 = '/^(0?\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/'; // 12:30:20
		$pattern2 = '/^(0?\d|1\d|2[0-3]):[0-5]\d$/'; // 12:30
		if ( (preg_match ($pattern1, $this->str)) || (preg_match ($pattern2, $this->str)) ){
			return true;
		}
	return false;
	}

	#@ return boolean
	# 두 날짜(2012-01-12 ~ 2012-01-13)가 정확한 기간인지 체크
	# 뒤에 날짜가 앞에 날짜보다 작으면 안됨
	# 두 날짜 데이타 타입(2012-01-12/2012-01-11)
	public function chkDatePeriod(){
		$date = explode('/', $this->str);
		$s = explode('-', $date[0]);
		$e = explode('-', $date[1]);

		$sres= mktime(0,0,0,$s[1],$s[2],$s[0]);
		$eres= mktime(0,0,0,$e[1],$e[2],$e[0]);
		if($sres>$eres) return false;
	return true;
	}

	#@ return boolean
	# JSON String 값인지 체크
	public function isJSON(){
		json_decode($this->str);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	#@ return boolean
	# 두 문자나 값이 서로 같은지 비교
	public function equals($s){
		$result = true;
		if(is_string($s)){ # 문자인지 체크
			if(strcmp($this->str, $s)) $result= false;
		}else{ # 문자외 체크
			if($this->str != $s ) $result = false;
		}
	return $result;
	}
}
?>
