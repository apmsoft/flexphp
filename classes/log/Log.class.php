<?php
namespace Flex\Log;

final class Log
{
    const VERSEION = '0.4';
    const MESSAGE_FILE   = 3; # 사용자 지정 파일에 저장
    const MESSAGE_ECHO   = 2; # 화면에만 출력
    const MESSAGE_SYSTEM = 0; # syslog 시스템 로그파일에 저장

    public static $message_type = 3;
    public static $logfile = 'log.txt';

    public static $logstyles = [];
    public static $debugs = ['d','v','i','w','e'];
    public static $options = [
        'datetime'   => true,   # 날짜 시간 출력
        'debug_type' => true,   # 디버그 타임 출력
        'newline'    => true    # 한줄내리기 출력
    ];

    # init
    public static function init(int $message_type = -1, string $logfile = null){
        self::$message_type = ($message_type > -1) ? $message_type : self::MESSAGE_FILE;
        self::$logfile = $logfile ?? 'log.txt';
    }

    # 출력 옵션 설정
    public static function options (array $options) : void 
    {
        if(is_array($options)){
            self::$options = array_merge(self::$options, $options);
        }
    }

    # 출력하고자 하는 옵션 선택
    public static function setDebugs(string $m1, ...$mores): void 
    {
        $debug_modes = [];
        $debug_modes[] = $m1;
        if(is_array($mores)){
            foreach($mores as $debug_type){
                $debug_modes[] = $debug_type;
            }
        }

        self::$debugs = $debug_modes;
    }

    # debug
    public static function d (string $message, ... $message2) : void
    {
        if(in_array('d', self::$debugs)){
            $output = $message.' | '.implode(' | ',$message2);
            self::print_('D', $output);
        }
    }

    # success
    public static function v (string $message, ... $message2) : void
    {
        if(in_array('v', self::$debugs)){
            $output = $message.' | '.implode(' | ',$message2);
            self::print_('V', $output);
        }
    }

    # info
    public static function i (string $message, ... $message2) : void
    {
        if(in_array('i', self::$debugs)){
            $output = $message.' | '.implode(' | ',$message2);
            self::print_('I', $output);
        }
    }

    # warning
    public static function w (string $message, ... $message2) : void
    {
        if(in_array('w', self::$debugs)){
            $output = $message.' | '.implode(' | ',$message2);
            self::print_('W', $output);
        }
    }

    # error
    public static function e (string $message, ... $message2) : void
    {
        if(in_array('e', self::$debugs)){
            $output = $message.' | '.implode(' | ',$message2);
            self::print_('E', $output);
        }
    }

    # print
    private static function print_ (string $debug_type, string $message) : void
    {
        $logfile = (self::$message_type == self::MESSAGE_FILE ) ? self::$logfile : null;
        $out_datetime   = (self::$options['datetime']) ? date('Y-m-d H:i:s').' ' : '';
        $out_debug_type = (self::$options['debug_type']) ? '>> '.$debug_type.' : ' : '';
        $out_newline    = (self::$options['newline']) ? PHP_EOL : '';

        if(self::$message_type == self::MESSAGE_ECHO){
            echo sprintf("%s%s%s%s", $out_datetime, $out_debug_type, addslashes($message), $out_newline);
        }else{
            error_log (
                sprintf("%s%s%s%s", $out_datetime, $out_debug_type, addslashes($message), $out_newline), 
                    self::$message_type, 
                        $logfile
            );
        }
    }
}
?>
