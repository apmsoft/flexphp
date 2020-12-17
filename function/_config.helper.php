<?php
/* ======================================================
| @UPDATE   : 2020-02-20
# purpose : 출력(output)
# 클래스와 기능이 동일
----------------------------------------------------------*/
use Flex\R\R;

# password
function password($passwd){
	$cipherEncrypt = new Fus3\Cipher\CipherEncrypt($passwd);
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

?>