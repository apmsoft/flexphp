<?php
namespace Flex\String;

# purpose : 문자을 변경하거나 더하거나 등 가공하는 역할을 한다.
class StringUtil{
	private $str;

	#@ void
	public function __construct(string $s){
		$this->str = $s;
	}

	#@ void
	# 기존문자에 문자 덮붙이기
	public function append(string|int $s) : void{
		$this->str .=$s;
	}

	#기본 문자 앞에 덮붙이기
	public function prepend(string|int $s) : void {
		$this->str = $s.$this->str;
	}

	# @ return String
	# 문자를 지정된길이부터 특정 문자로 변경하기
	# 010-4023-7046 => 010-****-7046
	# startNumber : 시작위치(index), endNumber : 길이만큼, chgString : 변형될 문자
	public function replace(int $startNumber, int $endNumber,string $chgString) : void
	{
		$result = '';
		$s = [];
		$sLength = strlen($this->str);
		$str =&$this->str;
		$cnt=0;
		$endNumber2 = ($startNumber-1)+$endNumber;
		for($i=0; $i<$sLength; $i++){
			if((Ord($str[$i])<=127)&&(Ord($str[$i])>=0)){$result .= ($cnt>=$startNumber && $cnt<=$endNumber2) ? $chgString : $str[$i]; $cnt++;}
			else if((Ord($str[$i])<=223)&&(Ord($str[$i])>=194)){$result .=($cnt>=$startNumber && $cnt<=$endNumber2) ? $chgString : $str[$i].$str[$i+1];$i+1; $cnt++;}
			else if((Ord($str[$i])<=239)&&(Ord($str[$i])>=224)){$result .=($cnt>=$startNumber && $cnt<=$endNumber2) ? $chgString : $str[$i].$str[$i+1].$str[$i+2];$i+2; $cnt++;}
			else if((Ord($str[$i])<=244)&&(Ord($str[$i])>=240)){$result .=($cnt>=$startNumber && $cnt<=$endNumber2) ? $chgString : $str[$i].$str[$i+1].$str[$i+2].$str[$i+3];$i+3; $cnt++;}
		}

		$this->str = $result;
	}

	# 문자 자르기
	# length : 문자길이
	public function cut(int $length, bool $is_apeend_cutstr = true, string $strip_tags = '<font><strong><b><strike>') : void
	{
		$result = '';
		$str =&$this->str;

		# 예외태그 허용
		if(trim($strip_tags)) {
			$str = strip_tags($str, $strip_tags);
		}

		# 허용된 태그와 문자 분리
		preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $str, $match);
		$match_str = (count($match[1])) ? implode(' ',$match[1]) : $str;
		
		# 태그를 제외한 문자 길이만 체크
		$len = strlen($match_str);
		if($len > $length)
		{
			for($i=0;$i<$length;$i++){
				if((Ord($match_str[$i])<=127)&&(Ord($match_str[$i])>=0)){$result .=$match_str[$i];}
				else if((Ord($match_str[$i])<=223)&&(Ord($match_str[$i])>=194)){$result .=$match_str[$i].$match_str[$i+1];$i+1;}
				else if((Ord($match_str[$i])<=239)&&(Ord($match_str[$i])>=224)){$result .=$match_str[$i].$match_str[$i+1].$match_str[$i+2];$i+2;}
				else if((Ord($match_str[$i])<=244)&&(Ord($match_str[$i])>=240)){$result .=$match_str[$i].$match_str[$i+1].$match_str[$i+2].$match_str[$i+3];$i+3;}
			}

			# 최종 길이에서 허용 태그 적용
			if(count($match[1])){
				foreach($match[1] as $idx => $match_val){
					$result = str_replace($match_val, $match[0][$idx], $result);
				}
			}

			# 끝에 줄임문자 붙이기
			if($is_apeend_cutstr) {
				$result.='...';
			}
		}

		if($result) $this->str = $result;
	}

	#@ return String
	#숫자를 특정문자 타입의 형태로 출력
	public function formatNumberPrintf(string $str='-') : void{
		$result = $this->str;
		$patterns = [
			4  => ['/(\d{1,1})(\d{1,3})/', '\1'.$str.'\2'],
			5  => ['/(\d{1,2})(\d{1,3})/', '\1'.$str.'\2'],
			6  => ['/(\d{1,3})(\d{1,3})/', '\1'.$str.'\2'],
			7  => ['/(\d{1,3})(\d{1,4})/', '\1'.$str.'\2'],
			8  => ['/(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'],
			10 => ['/(\d{1,3})(\d{1,3})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'],
			11 => ['/(\d{1,3})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'],
			12 => ['/(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'],
			13 => ['/(\d{1,1})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'],
			14 => ['/(\d{1,2})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'],
			15 => ['/(\d{1,3})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4'],
			16 => ['/(\d{1,4})(\d{1,4})(\d{1,4})(\d{1,4})/', '\1'.$str.'\2'.$str.'\3'.$str.'\4']
		];

		$length = strlen($result);
		if(isset($patterns[$length])){
			$this->str = preg_replace($patterns[$length][0], $patterns[$length][1],$this->str);
		}
	}

	#@ return String
	# utf-8 문자인지 체크 /--
	public function isUTF8Chg() : void
	{
		if(iconv("utf-8","utf-8",$this->str)==$this->str){}
		else $this->str = iconv('euc-kr','utf-8',$this->str);
	}

	#@ return String
	# euc-kr 문자인지 체크 /--
	public function isEuckrChg() : void
	{
		if(iconv("euc-kr","euc-kr",$this->str)==$this->str) {}
		else $this->str = iconv('utf-8','euc-kr',$this->str);
	}

	# 변경된 문자 값 돌려주기
	public function __get($propertyname){
		return $this->{$propertyname};
	}
}
?>