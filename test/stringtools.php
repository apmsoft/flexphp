<?php
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

use Flex\Annona\Log;
use Flex\Annona\Strings\StringTools;

# 기본값 MESSAGE_FILE, log.txt;
Flex\Annona\Log::init();
Flex\Annona\Log::init(Flex\Annona\Log::MESSAGE_ECHO);

# 10진 -> string
$data = (new StringTools("9744984499441004410144102445455454950455756"))->ascii2String()->data;
Log::d( $data );

# 16진 -> 10진 -> string
$data = (new StringTools( "612c31322c" ))->hex2Ascii( )->ascii2String()->data;
Log::d( $data );
?>