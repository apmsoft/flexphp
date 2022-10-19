<?php
use Flex\R\R;
use Flex\Log\Log;

$path = __DIR__;
require $path. '/config/config.inc.php';

# DEFINE
#define('_LOGFILE_','log.txt');

# Log setting
# 메세지출력 방법 : echo [Log::MESSAGE_ECHO], file [Log::MESSAGE_FILE]
# default 값: Log::MESSAGE_FILE, filename : log.txt
Log::init();
# Log::init(Log::MESSAGE_ECHO);
# Log::init(Log::MESSAGE_FILE, 'log.txt');

# 출력하고자 하는 디버그 타입 설정
# default 값 : 'i','d','v','w','e'
Log::setDebugs('i','v','e');

# 메세지 추가 옵션 출력 여부 설정
Log::options([
    'datetime'   => true, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# 메시지 단일 출력
Log::i($_SERVER['REMOTE_ADDR']);
/** 2022-10-18 16:46:28 >> I : 192.168.0.1 | */

# 멀티 메시지 출력
Log::i($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_METHOD'], $uri);
/** 2022-10-18 16:46:28 >> I : 192.168.0.1 | GET | /index.php/user/2 */

?>
