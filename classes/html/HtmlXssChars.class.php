<?php
namespace Flex\Html;

# purpose : xss 방지 및
class HtmlXssChars
{
	private $description;
	private $allow_tags = array();

	public function __construct(string $description){
		$this->description = $description;
	return $this;
	}

	#@ void
	# 허용 태그 설정
	public function setAllowTags(string $value) : void{
		if(is_array($value)) $this->allow_tags = array_merge($this->allow_tags,$value);
		else $this->allow_tags[] = $value;
	}

	# strip_tags
	public function cleanTags() : string{
		return strip_tags(htmlspecialchars_decode($this->description),implode('', $this->allow_tags));
	}

	#@ return String
	# Xss 태그 처리
	public function cleanXssTags() : string
	{
		$xss_tags = array(
			'@<script[^>]*?>.*?</script>@si',
			'@<style[^>]*?>.*?</style>@siU',
			'@<iframe[^>]*?>.*?</iframe>@si',
			'@<meta[^>]*?>.*?>@si',
			'@<form[^>]*?>.*?>@si',
			'@]script[^>]*?>.*?]/script>@si',	// [\xC0][\xBC]script>[code][\xC0][\xBC]/script>
			'/:*?expression\(.*?\)/si',
			'/:*?binding:(.*?)url\(.*?\)/si',
			'/javascript:[^\"\']*/si',
			'/vbscript:[^\"\']*/si',
			'/livescript:[^\"\']*/si',
			'@<![\s\S]*?--[ \t\n\r]*>@'// multi-line comments including CDATA
		);

		$event_tags = array(
			'dynsrc','datasrc','frameset','ilayer','layer','applet',
			'onabort','onactivate','onafterprint','onsubmit','onunload',
			'onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut',
			'onbeforedeactivate','onbeforeeditfocus','onbeforepaste','onbeforeprint',
			'onbeforeunload','onbeforeupdate','onblur','onbounce','oncellchange',
			'onchange','onclick','oncontextmenu','oncontrolselect','oncopy','oncut',
			'ondataavaible','ondatasetchanged','ondatasetcomplete','ondblclick',
			'ondeactivate','ondrag','ondragdrop','ondragend','ondragenter',
			'ondragleave','ondragover','ondragstart','ondrop','onerror','onerrorupdate',
			'onfilterupdate','onfinish','onfocus','onfocusin','onfocusout','onhelp',
			'onkeydown','onkeypress','onkeyup','onlayoutcomplete','onload','onlosecapture',
			'onmousedown','onmouseenter','onmouseleave','onmousemove','onmoveout',
			'onmouseover','onmouseup','onmousewheel','onmove','onmoveend','onmovestart',
			'onpaste','onpropertychange','onreadystatechange','onreset','onresize',
			'onresizeend','onresizestart','onrowexit','onrowsdelete','onrowsinserted',
			'onscroll','onselect','onselectionchange','onselectstart','onstart','onstop'
		);

		// 허용 태그 확인
		if(is_array($this->allow_tags)){
			$this->allow_tags = explode(',',strtr(implode(',',$this->allow_tags),['<'=>'','>'=>'']));
			$tmp_eventag= str_replace($this->allow_tags,'',implode('|',$event_tags));
			$event_tags = explode('|',$tmp_eventag);
		}

		return preg_replace($xss_tags, '', str_ireplace($event_tags,'_badtags',$this->description));
	}

	# 자동 링크 걸기
	public function setAutoLink() : string
	{
		$homepage_pattern = "/([^\"\'\=])(mms|market|http|https|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"\']+)/";
		$this->description = preg_replace($homepage_pattern,"\\1<a href='\\2://\\3' target='_blank'>\\2://\\3</a>",' '.$this->description);

		// 메일 치환
		$email_pattern = "/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/";
		return preg_replace($email_pattern,"\\1<a href='mailto:\\2@\\3>\\2@\\3'</a>", " ".$this->description);
	}

	# url 링크에 http가 있는지 확인후 붙여서 리턴해 주기
	public function setHttpUrl() : string
	{
		if($this->description) 
			$this->description = trim($this->description);

		if (strpos($this->description, 'http') ===false) {
			$this->description = 'http://'.$this->description;
		}
	return $this->description;
	}

	# code html highlight
	public function getXHtmlHighlight() : string 
	{
		$str = highlight_string($this->description, true);
		$str = preg_replace('#<font color="([^\']*)">([^\']*)</font>#', '<span style="color: \\1">\\2</span>', $str);
		return preg_replace('#<font color="([^\']*)">([^\']*)</font>#U', '<span style="color: \\1">\\2</span>', $str);
	}

	# 여러형태의 모양
	public function getContext(string $mode='XSS') : string
	{
		$this->description =stripslashes($this->description);
		switch(strtoupper($mode)){
			case 'TEXT':
				$this->description = strtr($this->description, ["&nbsp;"=>' ']);
				$this->description = strtr($this->description,["\r\n"=>"\n"]);
				$this->description = self::setAutoLink($this->description);
				$this->allow_tags  = ['<a>'];
				$this->description = $this->cleanTags();
				break;
			case 'XSS':
				$this->description = strtr($this->description,["\r\n"=>"\n"]);
				$this->description = strtr($this->description,["\n"=>"<br>"]);
				$this->description = strtr($this->description,["<br/>"=>"<br>"]);
				$this->description = strtr($this->description,["<br><br>"=>"<br>"]);
				$this->description = self::setAutoLink();
				$this->description = self::cleanXssTags();
				break;
			case 'HTML':
				$this->description = strtr($this->description,["\r\n"=>"\n"]);
				$this->description = strtr($this->description,["\n"=>"<br>"]);
				$this->description = self::setAutoLink($this->description);
				$this->description = htmlspecialchars($this->description);
				break;
			case 'XHTML' :
				$this->description = self::getXHtmlHighlight($this->description);
				$this->description = self::setAutoLink();
				break;
		}
	return $this->description;
	}
}
?>
