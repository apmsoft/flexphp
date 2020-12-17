<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @version : 1.0
----------------------------------------------------------*/
namespace Fus3\Html;

# purpose : 디바이스 단말기등에서 셀렉트 메뉴로 년월일등을 출력하기 위해서
class HtmlDateSelect extends HtmlButtonMenu
{
	public function __construct($name,$val,$arg=''){
		parent::__construct($name,$val,$arg='');
	}

	#@ return
	# 년 syear(시작년), eyear(종료년)
	public function getYear($syear, $eyear, $attribute=''){
		for($i=$syear; $i<=$eyear; $i++){
			$this->addParams($i, $i);
		}

	return $this->select($attribute);
	}

	#@ return
	# 월
	public function getMonth($attribute=''){
		for($i=1; $i<=12; $i++){
			$m=sprintf("%02d", $i);
			$this->addParams($m, $m);
		}

	return $this->select($attribute);
	}

	#@ return
	# 일
	public function getDay($attribute=''){
		for($i=1; $i<=31; $i++){
			$d=sprintf("%02d", $i);
			$this->addParams($d, $d);
		}

	return $this->select($attribute);
	}
}
?>