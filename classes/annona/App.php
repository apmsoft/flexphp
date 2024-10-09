<?php
namespace Flex\Annona;

# 접속에 따른 디바이스|브라우저등 정보
final class App
{
    public const __version      = '1.1';
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
            if(stristr($agent,'Linux')) App::$platform='Linux';
            else if(stristr($agent,'iPod')) App::$platform='iPod';
            else if(stristr($agent,'iPhone')) App::$platform='iPhone';
            else if(stristr($agent,'iPad')) App::$platform='iPad';
            else if(stristr($agent,'Windows Phone')) App::$platform='Windows Phone';
            else if(stristr($agent,'Windows CE')) App::$platform='Windows CE';
            else if(stristr($agent,'lgtelecom')) App::$platform='lgtelecom';
            else if(stristr($agent,'Android')) App::$platform='Android';
            else if(stristr($agent,'Macintosh')) App::$platform='Mac';
            else if(stristr($agent,'mac os x')) App::$platform='Mac';
            else if(stristr($agent,'Windows')) App::$platform='Windows';
            else if(stristr($agent,'Win32')) App::$platform='Windows';
        }

        #브라우저
        if (preg_match('/(MSIE|Opera|Firefox|Chrome|Safari|Opera|Netscape)/i', $agent)) {
            if(stristr($agent,'MSIE') && !stristr($agent,'Opera')) App::$browser='Explorer';
            else if(stristr($agent,'Firefox')) App::$browser='Firefox';
            else if(stristr($agent,'Chrome')) App::$browser='Chrome';
            else if(stristr($agent,'Safari')) App::$browser='Safari';
            else if(stristr($agent,'Opera')) App::$browser='Opera';
            else if(stristr($agent,'Netscape')) App::$browser='Netscape';
        }

        # 이전 접속경로
        if(isset($_SERVER['HTTP_REFERER']) && !is_null($_SERVER['HTTP_REFERER'])){
            App::$http_referer = $_SERVER['HTTP_REFERER'];
        }

        # 언어
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $hal = explode(',',strtr($_SERVER['HTTP_ACCEPT_LANGUAGE'],[';'=>',','-'=>'_']));
            foreach($hal as $v){
                if(strpos($v,App::$language.'_') !==false){
                    App::$locale = $v;
                    break;
                }
            }
            App::$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
        }

        # http | https
        $https = (isset($_SERVER['REQUEST_SCHEME'])) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"){
            $https = 'https';
        }

        # host
        App::$host = '';
        if(isset($_SERVER['HTTP_HOST'])){
            App::$host = sprintf("%s://%s",$https,$_SERVER['HTTP_HOST']);
        }

        # request METHOD
        App::$ip_address = App::get_client_ip();
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