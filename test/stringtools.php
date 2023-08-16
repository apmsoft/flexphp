<?php
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

use Flex\Annona\Log;
use Flex\Annona\Strings\StringTools;

# 기본값 MESSAGE_FILE, log.txt;
Flex\Annona\Log::init();
Flex\Annona\Log::init(Flex\Annona\Log::MESSAGE_ECHO);

# ascii -> string
$stringTools = new StringTools();
$data = $stringTools->convertAscii2String( "9744984499441004410144102445455454950455756" )->data;
Log::d( $data );
?>