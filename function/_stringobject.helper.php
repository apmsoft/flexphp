<?php
/* ======================================================
| @UPDATE   : 2014-11-25
# purpose : 출력(output)
# 클래스와 기능이 동일
# StringObject.class.php
----------------------------------------------------------*/
use Fus3\String\StringObject;

# 문자 자르기(UTF-8)
# 슬래쉬, HTML 태그 제거
function str_cut($str, $lenth){
	$strObj = new StringObject($str);
	$strObj->remove_html_specialchars();
	return $strObj->cut($lenth);
}
?>