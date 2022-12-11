<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();

# 화면에만 출력
Log::init(Log::MESSAGE_ECHO);

# REQUEST 접속 정보 : http://localhost:8001/flexphp/test/app.php
// App::init();
Log::d('platform',App::$platform);
Log::d('browser',App::$browser);
Log::d('host',App::$host);
Log::d('language',App::$language);
Log::d('http_referer',App::$http_referer ?? '');
Log::d('ip_address',App::$ip_address);
?>
