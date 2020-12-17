<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: https://www.fancyupsoft.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.0.3
----------------------------------------------------------*/
namespace Fus3\String;

# purpose : 문자을 변경하거나 더하거나 등 가공하는 역할을 한다.
class StringObject{
	private $str;

	#@ void
	public function __construct($s){
		if(empty($s)) return false;
		$this->str=$s;
	}

	#@ void
	# 기존문자에 문자 덮붙이기
	public function append($s){
		$this->str .=$s;
	}

	#@void
	#기본 문자 앞에 덮붙이기
	public function prepend($s){
		$this->str = $s.$this->str;
	}

	# @ return String
	# 문자를 지정된길이부터 특정 문자로 변경하기
	# 010-4023-7046 => 010-****-7046
	# startNumber : 시작위치, endNumber : 종료위치, chgString : 변형될 문자
	public function replace($startNumber,$endNumber,$chgString){
		$result = '';
		$s = array();
		$sLength = strlen($this->str);
		$endNumber2 = ($sLength < $endNumber) ? $sLength-$startNumber : $endNumber-$startNumber;
		$s[0] = substr($this->str,0,$startNumber);
		$s[1] = substr($this->str,$startNumber,$endNumber2);
		$s[2] = substr($this->str,$endNumber);

		# 바꿀문자로 체인징
		$chgReString = str_repeat($chgString, $endNumber2);
		$result = $s[0].$chgReString.$s[2];

	return $result;
	}

	#@ return String
	# 문자 자르기
	# length : 문자길이
	public function cut($length)
	{
		$result = '';
		$str =&$this->str;
		$len=strlen($str);
		if($len>$length)
		{
			for($i=0;$i<$length;$i++){
				if((Ord($str[$i])<=127)&&(Ord($str[$i])>=0)){$result .=$str[$i];}
				else if((Ord($str[$i])<=223)&&(Ord($str[$i])>=194)){$result .=$str[$i].$str[$i+1];$i+1;}
				else if((Ord($str[$i])<=239)&&(Ord($str[$i])>=224)){$result .=$str[$i].$str[$i+1].$str[$i+2];$i+2;}
				else if((Ord($str[$i])<=244)&&(Ord($str[$i])>=240)){$result .=$str[$i].$str[$i+1].$str[$i+2].$str[$i+3];$i+3;}
			}

			# 예외태그 허용
			$result = strip_tags($result, '<font><strong><strike>');
			$result = $result.'...';
		}
		if(!$result) $result = $str;
	return $result;
	}

	#@ return String
	# 원하는 문자 칼라 및 bold 등으로 바꿔준다
	public function chg_color_fontweight($keywords,$params=null){
		$param =array(
			'color'=>null,			# 칼라색을 넣어주세요 (#e4e5e6, red)
			'fontweight'=>true	# 글씨 굵게
		);
		if(is_array($params)){
			$param=array_merge($param,$params);
		}

		if(!$keywords) return $this->str;
		$keywords=urldecode($keywords);
		$keywords=trim($keywords);
		$keywords_arg=preg_split("/[\s,]+/", $keywords);
		$count=count($keywords_arg);
		for($i=0; $i<$count; $i++)
		{
			foreach($param as $k=>$v){
				if($v){
					if(!strcmp('fontweight',$k)) $this->str=str_replace($keywords_arg[$i],'<b>'.$keywords_arg[$i].'</b>',$this->str);
					else if(!strcmp('color',$k)) $this->str=str_replace($keywords_arg[$i],'<font color="'.$v.'">'.$keywords_arg[$i].'</font>',$this->str);
				}
			}
		}
	return $this->str;
	}

	#@ return string
	# 전화번호 하이픈 넣기
	public function formatPhoneNumber(){
		$result = '';
		if(strpos($this->str,'-') === false){
			$len = strlen($this->str);
			if($len <=8){
				$result = preg_replace("/(([0-9]{4}))([0-9]{4}$)/", "\\1-\\2", $this->str);
			}else{
				$result = preg_replace("/(0(?:2|[0-9]{2}))([0-9]+)([0-9]{4}$)/", "\\1-\\2-\\3", $this->str);
			}
		}
	return $result;
	}

	#@ return String
	#숫자를 특정문자 타입의 형태로 출력
	public function formatNumberPrintf($str='-'){
		$result = $this->str;
		$patterns = array(
			7=>array('/(\d{1,3})(\d{1,4})/', '\1'.$str.'\2'),
			8=>array('/(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'),
			10=>array('/(\d{1,3})(\d{1,3})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'),
			11=>array('/(\d{1,3})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'),
			12=>array('/(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'),
			13=>array('/(\d{1,1})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'),
			14=>array('/(\d{1,2})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'),
			15=>array('/(\d{1,3})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'),
			16=>array('/(\d{1,4})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4')
		);
		$length = strlen($result);
		if(isset($patterns[$length])){
			$result = preg_replace($patterns[$length][0], $patterns[$length][1],$this->str);
		}
	return $result;
	}

	# 슬래쉬, HTML 태그 제거
	public function remove_html_specialchars(){
		$this->str=htmlspecialchars(stripslashes(strip_tags(
			strtr(
				$this->str ,
				array('&nbsp;'=>'','&lt;'=>'','&gt;'=>'',"&#39;"=>'')
			)
		)));
	}

	#@ return String
	# utf-8 문자인지 체크 /--
	public function isUTF8Chg()
	{
		if(iconv("utf-8","utf-8",$this->str)==$this->str) return $this->str;
		else return iconv('euc-kr','utf-8',$this->str);
	}

	#@ return String
	# euc-kr 문자인지 체크 /--
	public function isEuckrChg()
	{
		if(iconv("euc-kr","euc-kr",$this->str)==$this->str) return $this->str;
		else return iconv('utf-8','euc-kr',$this->str);
	}

	# 변경된 문자 값 돌려주기
	public function __get($propertyname){
		return $this->{$propertyname};
	}
}
?>