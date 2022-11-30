<?php
namespace Flex\App;

# 접속에 따른 디바이스|브라우저등 정보
final class App
{
    public static $platform = 'Unknown';
    public static $browser = 'Unknown';
    public static $is_phone_device = false;
    public static $host;
    public static $lang;
    public static $http_referer =null;
    public static $ip_address = '';
    public static $version = '0.9.13Beta';

    public static function init() : void
    {
        # 기본 디바이스 인지 확인 하기 위한 체크
        $agent= (isset($_SERVER['HTTP_USER_AGENT'])) ?? '';

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

        # 디바이스 인지 체크
        if(preg_match( '/(Android|iPod|iPhone|Windows Phone|lgtelecom|Windows CE)/i', $agent)){
            self::$is_phone_device = true;
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
        self::$lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) : 'ko';

        # host url
        self::$host = '';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"){
            if(isset($_SERVER['SERVER_NAME'])){
                self::$host = 'https://'.$_SERVER['SERVER_NAME'];
            }else {
                self::$host = 'http://'.$_SERVER['SERVER_NAME'];
            }
        }

        # ip address
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

    # 애플사 제품인지 확인
    public static function is_apple_device() : string{
        return (preg_match( '/(iPod|iPhone|iPad)/', self::$platform)) ? 'true' : 'false';
    }
}
?>