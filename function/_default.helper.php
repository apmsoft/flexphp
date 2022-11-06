<?php
/* ======================================================
| @UPDATE   : 2020-02-20
# purpose : 출력(output)
# 클래스와 기능이 동일
----------------------------------------------------------*/
use Flex\R\R;
use Flex\Util\UtilUUID;

# password
function password($passwd){
	$cipherEncrypt = new \Flex\Cipher\CipherEncrypt($passwd);
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

# 토큰생성
function createToken($str){
	$result = '';
	$hash = $str ?? UtilUUID::create_uniqid();
	$result = UtilUUID::make($hash);
return $result;
}

function htmlXssChars($contents){
	$htmlXssChars = new \Flex\Html\HtmlXssChars($contents);
	return $htmlXssChars->getContext('TEXT');
}

# microtime 2 datetime
function convert2MTDT($duration){
	$result = '';
	$hours = (int)($duration/60/60);
	$minutes = (int)($duration/60)-$hours*60;
	$seconds = (int)$duration-$hours*60*60-$minutes*60;

return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
?>