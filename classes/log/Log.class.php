<?php
namespace Flex\Log;

final class Log
{
    const VERSEION = '0.2';
    const MESSAGE_FILE = 3;
    #const MESSAGE_EMAIL= 2;
    const MESSAGE_ECHO = 0;

    public static $message_type = 3;
    public static $logfile = 'log.txt';

    public static $logstyles = [];
    public static $debugs = ['d','v','i','w','e'];

    # init
    public static function init(int $message_type = -1, string $logfile = null){
        self::$message_type = ($message_type > -1) ? $message_type : self::MESSAGE_FILE;
        self::$logfile = $logfile ?? 'log.txt';
    }

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
        error_log (
            sprintf("%s >> %s : %s %s",  date('Y-m-d H:i:s'), $debug_type, $message, PHP_EOL), 
            self::$message_type, 
            $logfile
        );
    }
}
?>
