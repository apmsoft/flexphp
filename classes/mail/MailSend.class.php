<?php
/** ======================================================
// example
$file_args[]=array(
	'file_type'=>$file_row['file_type'],
	'sfilename'=>$file_row['sfilename'],
	'ofilename'=>$file_row['ofilename'],
	'file_size'=>$file_row['file_size'],
	'fullname'=>$_dir.'/'.$tables->talk.'/'.$date_dir.'/'.$file_row['sfilename']
);

$mailSend = new MailSend();
$mailSend->setHeaaderAttach($file_args);
$mailSend->setFrom($is_member['email'], $is_member['name']);
$mailSend->setDescription($_REQUEST['description']);
$mailSend->setAttachmentFiles($file_args);
$mailSend->setTo($emv, trim($emv));
$mailSend->send($is_member['name'].'님이 글을 등록하였습니다');
----------------------------------------------------------*/
namespace Flex\Mail;

class MailSend
{
	private $headers='', $description='';

	private $encoding='base64', $chrset='utf-8';
	private $content_type = 'html';
	private $boundary = '';

	#수신자 등록
	private $to_emails = [];

	# 전송 인코딩 방식 설정
	public function __construct(string $contype='', string $encoding='', string $chrset='')
	{
		# 인코딩 방식
		if(!empty($encoding))
			$this->encoding = $encoding;

		# 문자셋
		if(!empty($chrset))
			$this->chrset = $chrset;

		# Content-Type
		if(strcmp($contype,'html') || empty($contype))
			$this->content_type = 'plain';

		# 헤더 기본설정
		$this->boundary= md5(uniqid(time()));
		$this->headers = 'MIME-Version: 1.0' . "\r\n";

	}

	public function setHeaaderAttach($files){
		if(is_array($files) && count($files)>0)
			$this->headers= 'Content-Type: multipart/mixed; '.'boundary="------=_Part_001_'. $this->boundary .'"'. "\r\n";
		else
			$this->headers.= 'Content-Type: multipart/mixed; '.'boundary="------=_Part_000_'. $this->boundary .'"'. "\r\n";
	}

	# 보내는 사람 이메일 주소 및 성명
	public function setFrom($mail_addr, $name){
		$this->headers.= 'From: '. $name .' <'. $mail_addr .'>' . "\r\n";
	}

	# 추가헤더
	# ex) "Reply-To: info@my_site.com" | "Return-Path: info@my_site.com"
	public function setHeadersAppend($header_con){
		$this->headers.= $header_con . "\r\n";
	}

	# 메일 내용
	public function setDescription($message){
		$this->description = "\r\n";
		$this->description.= '------=_Part_000_'.$this->boundary. "\r\n";
		$this->description.= 'Content-Type: text/'.$this->content_type.'; charset='. strtoupper($this->chrset) ."\r\n";
		$this->description.= 'Content-Transfer-Encoding:'. $this->encoding . "\r\n\r\n";

		switch($this->encoding){
			case 'base64': $message = chunk_split(base64_encode(self::setCharet($message))); break;
			default : $message = self::setCharet($message); break;
		}
		$this->description.= $message . "\r\n\r\n";
		$this->description.= '------=_Part_000_'.$this->boundary.'--'. "\r\n";
	}

	# 첨부파일
	public function setAttachmentFiles($files=array())
	{
		$count = count($files);
		for ($i=0; $i<=$count; $i++)
		{
			$strObj = new StringObject($files[$i]['ofilename']);
			$full_filename	= $files[$i]['fullname'];
			$filename		= $strObj->isEuckrChg();
			$filetype		= $files[$i]['file_type'];

			$tmp_contents = '';
			if($fp = @fopen($full_filename, 'r'))
			{
				$tmp_contents = fread($fp, filesize($full_filename));
				$boundary_cnt_num = '1';//($i+1);

				# 파일 첨부 내용 덮입히기
				$boundary_cnt = sprintf("%03d",$boundary_cnt_num);
				$this->description.= '------=_Part_'.$boundary_cnt.'_'.$this->boundary. "\r\n";
				$this->description.= 'Content-Type: '.$filetype.'; name="'.'=?'.$this->chrset.'?B?'.base64_encode($filename).'?='.'"'."\r\n";
				$this->description.= 'Content-Disposition: inline; filename="'.'=?'.$this->chrset.'?B?'.base64_encode($filename).'?='.'"'."\r\n";
				$this->description.= 'Content-Transfer-Encoding: base64'."\r\n\r\n";

				$this->description.= chunk_split(base64_encode($tmp_contents));
				$this->description.= "\r\n";

				$this->description.= '------=_Part_'.$boundary_cnt.'_'.$this->boundary.'--'. "\r\n";
			}
		}
	}

	# @void
	# 수신자 등록 및 중복 체크
	public function setTo($name, $email)
	{
		$bool = true;
		if(is_array($this->to_mails))
		{
			$count = count($this->to_mails);
			for($i=0; $i<$count; $i++){
				if($this->to_mails[$i]['email'] == $email){
					$bool =true;
					break;
				}
			}
		}

		if($bool)
		{
			$this->to_mails[] = array(
				'name' =>$name,
				'email' => $email
			);
		}
	}

	# @return boolean
	# 메일 전송
	# ex) Mary <mary@example.com>, Kelly <kelly@example.com>
	public function send($title)
	{
		# 수신자 작업
		$to = '';
		$count = count($this->to_mails);
		for($i=0; $i<$count; $i++){
			$to.= $this->to_mails[$i]['name'].'<'.$this->to_mails[$i]['email'].'>,';
		}

		if($to)
		{
			$to = substr($to,0,-1);
			if(mail(
				self::setCharet($to),														# to
				'=?'.$this->chrset.'?B?'.base64_encode(self::setCharet($title)).'?=',		# title
				$this->description,															# contents
				$this->headers																# header
			)){ return true; }
			else return false;
		}
		else return false;
	}

	# 문자 출력 값이 utf-8인지 체크 후 변환하기
	public function setCharet($msg){
		# 전송된 값을 원하는 문자셋으로 변경
		if(iconv($this->chrset,$this->chrset,$msg)==$msg){
			return $msg;
		}else{
			return iconv($this->chrset, $this->chrset, $msg);
		}
	}
}
?>