<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.5.1
----------------------------------------------------------*/
namespace Flex\Out;

# define('_CHRSET_', 'utf-8');
# purpose : 문자를 출력하는데 필요한 불필요한 노가다를 막자
# Out::prints('메세지');
final class Out
{
	#@ void
	# 한줄 내림없이 문자 출력
	final public static function prints($str){
		echo self::checkSetCharet($str);
	}

	#@ void
	# 한줄 \n<br /> 내려서 출력
	final public static function prints_ln($str){
		echo self::checkSetCharet($str."\n<br />");
	}

	#@ void
	# 배열을 보기 좋게 출력
	final public static function prints_array($args){
		if(is_array($args)){
			echo '<xmp>';
			echo print_r($args);
			echo '</xmp>';
		}
	}
	final public static function prints_r($args){
		if(is_array($args)){
			echo '<xmp>';
			echo print_r($args);
			echo '</xmp>';
		}
	}

	#@ void
	# json 형식으로 출력
	#args = array('result'=>'1','message'=>'감사합니다');
	final public static function prints_json($args){

		if(!is_array($args)){
			die(__FILE__.':'.__CLASS__.' is not array');
		}

        //echo json_encode($args);
        self::prints_compress(json_encode($args, JSON_UNESCAPED_UNICODE));
		exit;
	}

	#@ void
	# json 형식으로 출력 /============
	# $jsonObj = (object) array();
	# $jsonObj->result = 'true';
	# $jsonObj->msg = $data;
	final public static function prints_json_obj($obj){
		if(!is_object($obj)){
			die(__FILE__.':'.__CLASS__.' is not object');
		}

		self::prints_compress(json_encode($obj));
		exit;
	}

	#@ void
	# gzip 압축
    final public static function prints_compress( $data ) {
    	// $_SERVER['HTTP_ACCEPT_ENCODING'];
        // $supportsGzip = strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== false;
        // if ( $supportsGzip ) {
        //     ob_start("ob_gzhandler");
        // }
        echo $data;
        exit;
    }

	#@ void
	# xml 형식으로 출력
	# args = array( // 반드시 데이타 값이 존재해야 한다.
	#	<chkcode>true</chkcode>
	#	<errnumber>100</errnumber>
	#);
	# message = 큰용량의 메세지 및 데이타
	final public static function prints_xml($args,$message=''){
		if(!is_array($args)){
			die(__FILE__.':'.__CLASS__.' is not array');
		}

		$xmldata = '<?xml version="1.0" encoding="'._CHRSET_.'" ?>'."\n";
		$xmldata .= '<result>'."\n";
		if(is_array($args)){
			foreach($args as $k=>$v){
				$xmldata .= '<'.$k.'>'.$v.'</'.$k.'>'."\n";
			}
		}
		$xmldata .= '<message><![CDATA['.$message.']]></message>'."\n";
		$xmldata .= '</result>';
        self::prints_compress($xmldata);
		exit;
	}

	# 문자 출력 값이 utf-8인지 체크 후 변환하기
	final public static function checkSetCharet($msg){
		# 전송된 값을 원하는 문자셋으로 변경
		if(iconv(_CHRSET_,_CHRSET_,$msg)==$msg)
			return $msg;
		else
			return iconv('euc-kr',_CHRSET_,$msg);
	}

	#@ void
	# html 형식으로 출력
	final public static function prints_html($message)
	{
        self::prints_compress($message);
		exit;
	}
}
?>
