<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @version :1.1
----------------------------------------------------------*/
namespace Flex\Out;

# purpose : sqlite 함수를 활용해 확장한다
# @ define('_CHRSET_','utf-8');
final class OutPane
{
	# 메세지 출력 및 지정 경로로 이동
	final public static function window_location($url,$msg=''){
		$outmsg = '';
		if($msg) $outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'window.location="'.$url.'";'."\n";
		self::error_report_prints($outmsg);
	}

	# 메세지 출력 후 뒤로 가기
	final public static function history_go($msg,$num=-1){
		$outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'history.go('.$num.');'."\n";
		self::error_report_prints($outmsg);
	}

	# 메세지 출력 후 팝업창 자신 닫기
	final public static function window_close($msg=''){
		$outmsg = '';
		if($msg) $outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'top.close();'."\n";
		self::error_report_prints($outmsg);
	}

	# 팝업 창에서 본페이지 이동 시키기
	final public static function opener_location($url){
		$outmsg.= 'opener.location.href="'.$url.'";'."\n";
		$outmsg.= 'window.close();';
		self::error_report_prints($outmsg);
	}

	# 자바스크립트 prompt 창을 통해 데이타 값 받기
	final public static function input_prompt($title,$defaultval=''){
		$title = self::checkSetCharet($title);
		$defaultval = self::checkSetCharet($defaultval);

		$outmsg = '';
		$outmsg.= 'var inputmsg;'."\n";
		$outmsg.= 'inputmsg = prompt("'.$title.'","'.$defaultval.'");'."\n";
		$outmsg.= 'document.write(inputmsg);'."\n";
		self::error_report_prints($outmsg);
	}

	# 자바스크립트 confirm 창을 통해 true/false 값 받기
	final public static function window_confirm($msg,$true_url,$false_url=''){
		$msg = self::checkSetCharet($msg);
		$true_url = self::checkSetCharet($true_url);
		if($false_url != '') $false_url = self::checkSetCharet($false_url);

		$outmsg = '';
		// $outmsg.= 'var inputmsg;'."\n";
		$outmsg.= 'if(confirm("'.$msg.'")) {'."\n";
		$outmsg.= 'window.location="'.$true_url.'";'."\n";

		if($false_url != '') {
			$outmsg.= '} else {';
			$outmsg.= 'window.location="'.$false_url.'";'."\n";
		}

		$outmsg.= '}';
		self::error_report_prints($outmsg);
	}

	# 문자 출력 값이 utf-8인지 체크 후 변환하기
	final public static function checkSetCharet($msg){
		# 전송된 값을 원하는 문자셋으로 변경
		if(iconv(_CHRSET_,_CHRSET_,$msg)==$msg)
			return $msg;
		else
			return iconv('euc-kr',_CHRSET_,$msg);
	}

	final public static function error_report_prints($outmsg){
		$printMsg = '<meta http-equiv="Content-Type" content="text/html; charset='._CHRSET_.'" />'."\n";
		$printMsg .= '<script type="text/javascript" language="javascript">'."\n";
		$printMsg .= $outmsg;
		$printMsg .= '</script>';
		echo $printMsg;
		exit;
	}
}
?>
