<?php
use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

$path = __DIR__;
require $path. '/config/config.inc.php';

# Log setting
Log::init(Log::MESSAGE_ECHO);
Log::setDebugs('i','d','v','e');

?>