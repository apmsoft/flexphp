<?php
namespace Flex\R;

use \ArrayObject;
use \ErrorException;

final class Log
{
    const MESSAGE_FILE = 3;
    #const MESSAGE_EMAIL= 2;
    const MESSAGE_ECHO = 0;

    public static $message_type = 3;
    public static $logfile = 'log.txt';

    public static $logstyles = [];

    # init
    public static function init(int $message_type = -1, string $logfile = null){
        self::$message_type = ($message_type > -1) ? $message_type : self::MESSAGE_FILE;
        self::$logfile = $logfile ?? 'log.txt'
    }

    # debug
    public static function d (string $message, ... $message2) : void{
        $output = $message.' | '.implode(' | ',$message2);
        self::print_('D', $output);
    }

    # info
    public static function i (string $message, ... $message2) : void{
        $output = $message.' | '.implode(' | ',$message2);
        self::print_('I', $output);
    }

    # warning
    public static function w (string $message, ... $message2) : void{
        $output = $message.' | '.implode(' | ',$message2);
        self::print_('W', $output);
    }

    # error
    public static function e (string $message, ... $message2) : void{
        $output = $message.' | '.implode(' | ',$message2);
        self::print_('E', $output);
    }

    private static function print_ (string $debug_type, string $message) : void
    {
        $logfile = (self::$message_type == self::MESSAGE_FILE ) ? self::$logfile : null;
        error_log (sprintf("%s >> %s : %s", date('Y-m-d H:i:s'),$debug_type,$message), self::$message_type, $logfile);
    }
}
?>
