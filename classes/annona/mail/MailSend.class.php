<?php
namespace Flex\Annona\Mail;

class MailSend
{
    public const __version = '0.1';
    private $to           = ['email'=>'', 'name'=>''];
    private $from         = ['email'=>'', 'name'=>''];
    private $headers_args = [];
    private $message      = '';
    private $content_type = '';
    private $charset      = 'utf-8';
    private $encoding     = '8bit';
    private $boundary;

    public function __construct(){
        $this->boundary='=_Part_' . md5(rand() . microtime());
        $this->headers = 'MIME-Version: 1.0' . "\r\n";
    }

    public function setTo($name,$email){
        $this->to['email'] = $email;
        $this->to['name'] = $name;
    }

    public function setFrom($email, $name){
        $this->from['email'] = $email;
        $this->from['name'] = $name;
    }

    public function setHeader($key, $value){
        if(!isset($this->headers[$key])){
            $this->headers_args[$key] = $value;
        }
    }

    public function setTextHtml($content){
        $this->headers.= 'Content-Type: text/html; charset='. strtoupper($this->charset).'; format=flowed' ."\r\n";
        $this->message.= $this->encodeMessage($content) . "\r\n";
    }

    public function setTextPlain($content){
        $this->headers.= 'Content-Type: text/plain; charset='. strtoupper($this->charset) ."\r\n";
        $this->message.= $this->encodeMessage($content) . "\r\n";
    }

    private function encodeMessage($message){
        switch($this->encoding){
            case 'base64':
                $message = chunk_split(base64_encode($this->setCharet($message)));
                $this->headers.= 'Content-Transfer-Encoding: '.$this->encoding."\n";
            break;
            default : $message = $this->setCharet($message); break;
        }
    return $message;
    }

    # 문자 출력 값이 utf-8인지 체크 후 변환하기
    public function setCharet($msg){
        if($this->charset=='euc-kr') return $this->isEuckrChg($msg);
        return $this->isUTF8Chg($msg);
    }

    #@ return String
    # utf-8 문자인지 체크 /--
    public function isUTF8Chg($msg)
    {
        if(iconv("utf-8","utf-8",$msg)==$msg) return $msg;
        else return iconv('euc-kr','utf-8',$msg);
    }

    #@ return String
    # euc-kr 문자인지 체크 /--
    public function isEuckrChg($msg)
    {
        if(iconv("euc-kr","euc-kr",$msg)==$msg) return $msg;
        else return iconv('utf-8','euc-kr',$msg);
    }

    public function send($subject)
    {
        # to
        $to = '=?'.strtoupper($this->charset).'?B?'.base64_encode($this->setCharet($this->to['name'])).'?= <'.$this->to['email'].'>'. "\n";
        //$to = base64_encode($this->setCharet($this->to['name'])).'<'.$this->to['email'].'>';
        //$this->headers .='To: '.$to. "\n";

        # from
        $this->headers .= 'From: =?'.strtoupper($this->charset).'?B?'.base64_encode($this->setCharet($this->from['name'])).'?= <'.$this->from['email'].'>'. "\n";
        //$this->headers .= 'Reply-To: =?'.strtoupper($this->charset).'?B?'.base64_encode($this->setCharet($this->from['name'])).'?= <'.$this->from['email'].'>'."\n";

        # subject
        $subject= '=?'.strtoupper($this->charset).'?B?'.base64_encode($this->setCharet($subject)).'?=';

        #send
        if(mail($to,$subject,$this->message,$this->headers)){ return true; }else{
            throw new \ErrorException('mail send error');
        }
    }
}
?>