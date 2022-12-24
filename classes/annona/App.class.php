<?php
namespace Flex\Annona;

# 접속에 따른 디바이스|브라우저등 정보
final class App
{
    public static $platform     = 'Nan';
    public static $browser      = 'Nan';
    public static $host;
    public static $language     = 'ko';
    public static $locale       = 'ko_KR';
    public static $http_referer = null;
    public static $ip_address   = '';
    public static $protocol     = 'Nan';
    public static $version      = '1.0';

    public static function init() : void
    {
        # 기본 디바이스 인지 확인 하기 위한 체크
        $agent= (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';

        # platform
        if (preg_match('/(Linux|Android|Macintosh|Mac os x|Windows|Win32|iPod|iPhone|Windows Phone|lgtelecom|Windows CE)/i', $agent)) {
            if(stristr($agent,'Linux')) self::$platform='Linux';
            else if(stristr($agent,'iPod')) self::$platform='iPod';
            else if(stristr($agent,'iPhone')) self::$platform='iPhone';
            else if(stristr($agent,'iPad')) self::$platform='iPad';
            else if(stristr($agent,'Windows Phone')) self::$platform='Windows Phone';
            else if(stristr($agent,'Windows CE')) self::$platform='Windows CE';
            else if(stristr($agent,'lgtelecom')) self::$platform='lgtelecom';
            else if(stristr($agent,'Android')) self::$platform='Android';
            else if(stristr($agent,'Macintosh')) self::$platform='Mac';
            else if(stristr($agent,'mac os x')) self::$platform='Mac';
            else if(stristr($agent,'Windows')) self::$platform='Windows';
            else if(stristr($agent,'Win32')) self::$platform='Windows';
        }

        #브라우저
        if (preg_match('/(MSIE|Opera|Firefox|Chrome|Safari|Opera|Netscape)/i', $agent)) {
            if(stristr($agent,'MSIE') && !stristr($agent,'Opera')) self::$browser='Explorer';
            else if(stristr($agent,'Firefox')) self::$browser='Firefox';
            else if(stristr($agent,'Chrome')) self::$browser='Chrome';
            else if(stristr($agent,'Safari')) self::$browser='Safari';
            else if(stristr($agent,'Opera')) self::$browser='Opera';
            else if(stristr($agent,'Netscape')) self::$browser='Netscape';
        }

        # 이전 접속경로
        if(isset($_SERVER['HTTP_REFERER']) && !is_null($_SERVER['HTTP_REFERER'])){
            self::$http_referer = $_SERVER['HTTP_REFERER'];
        }

        # 언어
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $hal = explode(',',strtr($_SERVER['HTTP_ACCEPT_LANGUAGE'],[';'=>',','-'=>'_']));
            foreach($hal as $v){
                if(strpos($v,self::$language.'_') !==false){
                    self::$locale = $v;
                    break;
                }
            }
            self::$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
        }

        # http | https
        $https = (isset($_SERVER['REQUEST_SCHEME'])) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"){
            $https = 'https';
        }

        # host
        self::$host = '';
        if(isset($_SERVER['HTTP_HOST'])){
            self::$host = sprintf("%s://%s",$https,$_SERVER['HTTP_HOST']);
        }

        # request METHOD
        self::$ip_address = self::get_client_ip();
    }

    #@ return string
    # ip 주소 확인
    public static function get_client_ip() : string
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
}
?>