<?php
namespace Flex\Annona\Out;

final class OutPane
{
	public const __version = '2.2';
	public static string $chrset = 'utf-8';

	# 메세지 출력 및 지정 경로로 이동
	final public static function window_location(string $url,string $msg='') : void
	{
		$outmsg = '';
		if($msg) $outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'window.location="'.$url.'";'."\n";
		OutPane::error_report_prints($outmsg);
	}

	# 메세지 출력 후 뒤로 가기
	final public static function history_go(string $msg,int $num=-1) : void 
	{
		$outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'history.go('.$num.');'."\n";
		OutPane::error_report_prints($outmsg);
	}

	# 메세지 출력 후 팝업창 자신 닫기
	final public static function window_close(string $msg='') : void
	{
		$outmsg = '';
		if($msg) $outmsg= 'window.alert("'.$msg.'");'."\n";
		$outmsg.= 'top.close();'."\n";
		OutPane::error_report_prints($outmsg);
	}

	# 팝업 창에서 본페이지 이동 시키기
	final public static function opener_location(string $url) : void 
	{
		$outmsg.= 'opener.location.href="'.$url.'";'."\n";
		$outmsg.= 'window.close();';
		OutPane::error_report_prints($outmsg);
	}

	# 자바스크립트 prompt 창을 통해 데이타 값 받기
	final public static function input_prompt(string $title,string $defaultval='') : void 
	{
		$title = self::checkSetCharet($title);
		$defaultval = self::checkSetCharet($defaultval);

		$outmsg = '';
		$outmsg.= 'var inputmsg;'."\n";
		$outmsg.= 'inputmsg = prompt("'.$title.'","'.$defaultval.'");'."\n";
		$outmsg.= 'document.write(inputmsg);'."\n";
		OutPane::error_report_prints($outmsg);
	}

	# 자바스크립트 confirm 창을 통해 true/false 값 받기
	final public static function window_confirm(string $msg,string $true_url,string $false_url='') : void 
	{
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
		OutPane::error_report_prints($outmsg);
	}

	# 문자 출력 값이 utf-8인지 체크 후 변환하기
	final public static function checkSetCharet(string $msg) : string 
	{
		# 전송된 값을 원하는 문자셋으로 변경
		if(iconv(OutPane::$chrset,OutPane::$chrset,$msg)==$msg)
			return $msg;
		else
			return iconv('euc-kr',OutPane::$chrset,$msg);
	}

	final public static function error_report_prints($outmsg) : void
	{
		$printMsg = '<meta http-equiv="Content-Type" content="text/html; charset='.OutPane::$chrset.'" />'."\n";
		$printMsg .= '<script type="text/javascript" language="javascript">'."\n";
		$printMsg .= $outmsg;
		$printMsg .= '</script>';
		echo $printMsg;
		exit;
	}
}
?>
