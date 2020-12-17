<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @version : 1.0
----------------------------------------------------------*/
namespace Flex\Html;

# purpose : 체크박스, 라디오메뉴, 셀렉트 메뉴등을 출력하는 목적으로 사용
class HtmlButtonMenu{
	private $args = array();	# 배열
	private $chkValue;			# 선택값
	private $name; 				# name 및 id 명

	# name : 필드명
	# val : 체크(선택) 값
	# arg : 배열 값을 한번에 등록
	public function __construct($name,$val,$arg=''){
		$this->name = $name;
		$this->chkValue = $val;

		if(is_array($arg)){
			$this->args = $this->args + $arg;
		}
	}

	# 프라퍼티에 값을 추가하기
	public function addParams($key, $val){
		$this->args[$key] = $val;
	}

	# 배열 하나값 만 추출
	public function __get($propertyname){
		try{
			return $this->{$propertyname};
		}catch(Exception $e){
			Out::prints_ln($e->getMessage());
		}
	}

	# @@ 라디오 메뉴
	# 메뉴 시각적인 정렬을 위해 table 태그를 활용
	# cellcnt : 가로정렬갯수
	# ^^^ $attribute : 기타 필요한 태그들을 넣어준다 (onClick="chkvalue('{%v%}');" )
	# ^^^ {%v%} -> 밸류값,{%k%} -> 키값, {%name%} -> 네임값, {%id%} -> 아이디값
	/**
	$guide_obj = new HtmlButtonMenu('guide', $row['guide']);
	$guide_obj->addParams("필요","필요");
	$guide_obj->addParams("불필요","불필요");
	$out->prints($guide_obj->radio($cellcnt=2,$attribute=''));
	*/
	public function radio($cellcnt=1,$attribute=''){
		$params = '';
		$outHtml = '';//'<table border="0"><tr>';
		$i=1;
		foreach($this->args as $k => $v)
		{
			# 값 체크
			$checked = '';
			if($k == $this->chkValue){ $checked = 'checked="checked"'; }

			$tagId = $this->name.''.$i;
			if(!empty($attribute)){
				$params = str_replace('{%k%}',$k,$attribute);
				$params = str_replace('{%v%}',$v,$params);
				$params = str_replace('{%name%}',$this->name,$params);
				$params = str_replace('{%id%}',$tagId,$params);
			}
			$outHtml.= '<input type="radio" name="'.$this->name.'" value="'.$k.'" id="'.$tagId.'" '.$checked.' '.$params.'   style="padding-top: 10px;"/>';
			$outHtml.= '<label for="'.$tagId.'" style="padding-right: 10px; padding-left: 5px;">'.$v.'</label>';
			$outHtml.= ($i % $cellcnt== 0) ? '<br />'."\n" : '';
		$i++;
		}
		//$outHtml.= '</tr></table>';
	return $outHtml;
	}

	# @@ 셀렉트 메뉴
	# ^^^ $attribute : 기타 필요한 태그들을 넣어준다 (onClick="chkvalue('{%v%}');" )
	# ^^^ {%v%} -> 밸류값,{%k%} -> 키값, {%name%} -> 네임값, {%id%} -> 아이디값
	public function select($attribute=''){
		$params = '';
		if(!empty($attribute)){
			$params = str_replace('{%k%}',$k,$attribute);
			$params = str_replace('{%v%}',$v,$params);
			$params = str_replace('{%name%}',$this->name,$params);
			$params = str_replace('{%id%}',$this->name,$params);
		}
		$outHtml = '<select name="'.$this->name.'" id="'.$this->name.'" '.$params.'>';
		foreach($this->args as $k => $v)
		{
			# 값 체크
			$selected = '';
			if($k == $this->chkValue){ $selected = 'selected="selected"'; }
			$outHtml.= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		$outHtml.= '</select>';
	return $outHtml;
	}

	# @@ 체크박스 메뉴
	# 메뉴 시각적인 정렬을 위해 table 태그를 활용
	# cellcnt : 가로정렬갯수
	# chkValue : 선택값(복수값 허용 -> 'a,b,c')
	# ^^^ $attribute : 기타 필요한 태그들을 넣어준다 (onClick="chkvalue('{%v%}');" )
	# ^^^ {%v%} -> 밸류값,{%k%} -> 키값, {%name%} -> 네임값, {%id%} -> 아이디값
	/**
	$bed_obj = new HtmlButtonMenu('bed', '');
	$bed_obj->addParams("호텔","호텔");
	$bed_obj->addParams("민박","민박");
	$bed_obj->addParams("기타","기타");
	$out->prints($bed_obj->checkbox($cellcnt=3,$attribute=''));
	*/
	public function checkbox($cellcnt=1,$attribute=''){
		$valargs[0] =& $this->chkValue;
		if(strpos($this->chkValue,',') !== false){
			$valargs = explode(',',$this->chkValue);
		}

		$params = '';
		$outHtml = ''.//'<table border="0"><tr>';
		$i=1;
		foreach($this->args as $k => $v)
		{
			# 값 체크
			$checked = '';
			if(array_search($k, $valargs) !== false){ $checked = 'checked="checked"'; }

			$tagId = $this->name.''.$i;
			if(!empty($attribute)){
				$params = str_replace('{%k%}',$k,$attribute);
				$params = str_replace('{%v%}',$v,$params);
				$params = str_replace('{%name%}',$this->name,$params);
				$params = str_replace('{%id%}',$tagId,$params);
			}
			$outHtml.= '<input type="checkbox" name="'.$this->name.'[]" value="'.$k.'" '.$checked.'" id="'.$tagId.'" '.$params.' />';
			$outHtml.= '<label for="'.$tagId.'">'.$v.'</label>';
			$outHtml.= ($i % $cellcnt== 0) ? '</br>'."\n" : '';
		$i++;
		}
		//$outHtml.= '</tr></table>';
	return $outHtml;
	}
}
?>
