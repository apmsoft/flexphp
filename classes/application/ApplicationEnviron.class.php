<?php
/** ===========================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @Editor   : Sublime Text
| @Version  : 1.1.2
-------------------------------------------------------*/
namespace Fus3\Application;

use \ArrayAccess;

# 접속에 따른 디바이스|브라우저등 정보
final class ApplicationEnviron implements ArrayAccess
{
    private $platform = 'Unknown';
    private $browser = 'Unknown';
    private $is_phone_device = false;
    private $host;
    private $lang;
    private $http_referer =null;
    private $ip_address = '';
    private $version = '0.9.13Beta';
    private $vars = array();

    public function __construct()
    {
        # 기본 디바이스 인지 확인 하기 위한 체크
        $agent='';
        $agent=(isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER[ 'HTTP_USER_AGENT'] : '';

        # platform
        if (preg_match('/(Linux|Android|Macintosh|Mac os x|Windows|Win32|iPod|iPhone|Windows Phone|lgtelecom|Windows CE)/i', $agent)) {
            if(stristr($agent,'Linux')) $this->platform='Linux';
            else if(stristr($agent,'iPod')) $this->platform='iPod';
            else if(stristr($agent,'iPhone')) $this->platform='iPhone';
            else if(stristr($agent,'iPad')) $this->platform='iPad';
            else if(stristr($agent,'Windows Phone')) $this->platform='Windows Phone';
            else if(stristr($agent,'Windows CE')) $this->platform='Windows CE';
            else if(stristr($agent,'lgtelecom')) $this->platform='lgtelecom';
            else if(stristr($agent,'Android')) $this->platform='Android';
            else if(stristr($agent,'Macintosh')) $this->platform='Mac';
            else if(stristr($agent,'mac os x')) $this->platform='Mac';
            else if(stristr($agent,'Windows')) $this->platform='Windows';
            else if(stristr($agent,'Win32')) $this->platform='Windows';
        }

        # 디바이스 인지 체크
        if(preg_match( '/(Android|iPod|iPhone|Windows Phone|lgtelecom|Windows CE)/i', $agent)){
            $this->is_phone_device = true;
        }

        #브라우저
        if (preg_match('/(MSIE|Opera|Firefox|Chrome|Safari|Opera|Netscape)/i', $agent)) {
            if(stristr($agent,'MSIE') && !stristr($agent,'Opera')) $this->browser='Explorer';
            else if(stristr($agent,'Firefox')) $this->browser='Firefox';
            else if(stristr($agent,'Chrome')) $this->browser='Chrome';
            else if(stristr($agent,'Safari')) $this->browser='Safari';
            else if(stristr($agent,'Opera')) $this->browser='Opera';
            else if(stristr($agent,'Netscape')) $this->browser='Netscape';
        }

        # 이전 접속경로
        if(isset($_SERVER['HTTP_REFERER']) && !is_null($_SERVER['HTTP_REFERER'])){
            $this->http_referer = $_SERVER['HTTP_REFERER'];
        }

        # 언어
        $this->lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) : 'ko';

        # host url
        $this->host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://'.$_SERVER['SERVER_NAME'] : 'http://'.$_SERVER['SERVER_NAME'];

        # ip address
        $this->ip_address = self::get_client_ip();
    }

    #@ return string
    # ip 주소 확인
    private function get_client_ip()
    {
        $result = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) $result = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $result = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED'])) $result = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) $result = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED'])) $result = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR'])) $result = $_SERVER['REMOTE_ADDR'];

    return $result;
    }

    #@ return boolean
    # 애플사 제품인지 확인
    public function is_apple_device(){
        if(preg_match( '/(iPod|iPhone|iPad)/', $this->platform)) return 'true';
        else return 'false';
    }

    #@ interface : ArrayAccess
    public function offsetSet($offset, $value){
        if(is_array($value)){
            if(isset($this->vars[$offset])) $this->vars[$offset] = array_merge($this->vars[$offset],$value);
            else $this->vars[$offset] = $value;
        }
        else{ $this->vars[$offset] = $value; }
    }

    #@ interface : ArrayAccess
    public function offsetExists($offset){
        if(isset($this->vars[$offset])) return isset($this->vars[$offset]);
        else return isset($this->vars[$offset]);
    }

    #@ interface : ArrayAccess
    public function offsetUnset($offset){
        if(self::offsetExist($offset)) unset($this->vars[$offset]);
        else unset($this->vars[$offset]);
    }

    #@ interface : ArrayAccess
    public function offsetGet($offset) {
        return isset($this->vars[$offset]) ? $this->vars[$offset] : $this->vars[$offset];
    }

    #@ void
    public function __set($name, $value){
        if(property_exists(__CLASS__,$name)){
            return $this->{$name} = $value;
        }
    }

    #@ return
    public function __get($name) {
        if(property_exists(__CLASS__,$name)){
            return $this->{$name};
        }
    }
}
?>