<?php
/* ======================================================
| @UPDATE   : 2020-02-20
# purpose : 출력(output)
# 클래스와 기능이 동일
----------------------------------------------------------*/
use Fus3\Paging\PagingRelation;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Out\Out;
use Fus3\R\R;
use Fus3\String\StringRandom;
use Fus3\Html\HtmlXssChars;

# 데이터 쿼리 limit 및 페이지 만들기
function limit($total_record, $page, $page_limit, $page_block_count){
	$paging = new PagingRelation('', $total_record, $page);
	$paging->setQueryCount( $page_limit, $page_block_count );
	$paging->setBuildQuery( array(
		'page' => $page
	));
	$paging->buildPageRelation();
	$result = array(
		'totalPage'       => $paging->totalPage,
		'limitStart'      => $paging->qLimitStart,
		'limitEnd'        => $paging->pageLimit,
		'paging_relation' => $paging->printRelation()
	);
return	$result;
}

# timestamp -> Y-m-d H:i:s
function timetodatetime($timestamp){
	return (!empty($timestamp)) ? date('Y-m-d H:i:s',$timestamp) : 0;
}

# 권한체크
function chk_authority($authority_level)
{
	# 로그인 상태 체크
	if($authority_level >0 ){
		if(!$_SESSION['auth_id'] || $_SESSION['auth_level'] < $authority_level){
			Out::prints_json(array('result'=>'false','msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
		}
	}

	# 관리자
	if($authority_level >= _AUTH_SUPERADMIN_LEVEL)
	{
		# 로그인 상태 체크
		if(!isset($_SESSION['aduuid'])) 
			Out::prints_json(array('result'=>'false','msg_code'=>'w_aduuid','msg'=>R::$sysmsg['w_aduuid']));

		# [세션보안]로그인 성공시 등록한 ip와 접속자 ip가 동일한지 체크한다.
		if($_SESSION['auth_id']){
			if(strcmp($_SESSION['auth_ip'],$_SERVER['REMOTE_ADDR'])){
				Out::prints_json(array('result'=>'false','msg_code'=>'w_authority_ip','msg'=>R::$sysmsg['w_authority_ip']));
			}
		}
	}
return $authority_level;
}

# password
function password($passwd){
	$cipherEncrypt = new CipherEncrypt($passwd);
return $cipherEncrypt->_md5_base64();
}

# 비회원아이디 만들기
function createUserID($device_uuid,$device_model,$glue_str){
	$result = '';
	if($device_uuid){
		$id = sprintf("%s%s",$device_uuid,strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $device_model)) );
		$result = sprintf("%s%s",$id, $glue_str);
	}
return $result;
}

# 업로드토큰생성
function create_upload_token($str){
	$result = $str;
	#echo $str."\r\n";
	if(trim($str)){
		$result = $str.'_'.strtr(microtime(), array(' '=>'','.'=>'','_'=>'_'));
	}
return $result;
}

# xss 내용
function contextType($val,$mode){
	$markup_mode = ($mode) ? $mode : 'XSS';
	$htmlXssChars= new HtmlXssChars($val);
return $htmlXssChars->getContext($markup_mode);
}

# 랜덤 토큰 발행
function randomToken($argv, $length){
	$result = '';
	if(is_array($argv) && $length>0){
		$stringRandom = new StringRandom($argv);
		$result = $stringRandom->arrayRand($length);
	}
return $result;
}

# 문자 문자 병합
function stringMerge ($str1, $str2, $str3){
	return $str1.$str2.$str3;
}

# 다중 배열안의 키밸류값들을 다중 배열안의 단순 키값으로 바꾸기
# 셀렉트을 위해
function convert2SingleArray($multi_args, $key_column, $title_column){
	$result = array();
	if(is_array($multi_args)){
		foreach($multi_args as $ak => $aargs){
			if(isset($aargs[$key_column]) && isset($aargs[$title_column])){
				$_key = $aargs[$key_column];
				$_title = $aargs[$title_column];
				$result[$_key] = $_title;
			}
		}
	}
return $result;
}

# array('a','b')
# 밸류 값을 키로 옮겨 array('a'=>'a','b'=>'b')로 바꾸기
function convertArrayVal2KeyVV($argv){
	$result = array();
	if(is_array($argv)){
		foreach($argv as $k => $v){
			$result[$v] = $v;
		}
	}
return $result;
}

# 싱글 배열키 중에서 키에 해당하는 값만 추출하기
function getValueInArray($arg, $key){
	$result = '';
	if(is_array($arg)){
		if(isset($arg[$key])){
			$result = $arg[$key];
		}
	}
return $result;
}

#심플계산기
function simpleCalculate($no1,$operation,$no2){
	$result = 0;
	switch($operation){
		case '+': 
		$result = ($no1 + $no2); 
		break;
	
		case '-':
		$result = ($no1 - $no2);
		break;
	
		case '/':
		$result = ($no1 / $no2);
		break;    
	
		case '*':
		$result = ($no1 * $no2);
		break;	
	}
return $result;
}
?>