<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Http\HttpUrlFilter;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';
#require $path. '/vendor/autoload.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);



Log::d('http ->', (new HttpUrlFilter('test.com'))->httpPrefix( glue: 'http')->url);

Log::d('https ->', (new HttpUrlFilter('www.test.com'))->httpPrefix( glue: 'https')->url);

Log::d('http|https ->', (new HttpUrlFilter('www.test.com'))->httpPrefix( glue: '')->url);


Log::d('www ->', (new HttpUrlFilter('game.com'))->wwwPrefix()->url);

Log::d('http -> www ->', (new HttpUrlFilter('game.com'))->httpPrefix( glue: 'http')->wwwPrefix()->url);
Log::d('https -> www ->', (new HttpUrlFilter('game.com'))->httpPrefix( glue: 'https')->wwwPrefix()->url);
Log::d('http|https -> www ->', (new HttpUrlFilter('game.com'))->httpPrefix( glue: '')->wwwPrefix()->url);
?>