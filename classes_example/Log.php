<?php
use Flex\App\App;
use Flex\R\R;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# Log setting
# MESSAGE_FILE   = 3; # 사용자 지정 파일에 저장
# MESSAGE_ECHO   = 2; # 화면에만 출력
# MESSAGE_SYSTEM = 0; # syslog 시스템 로그파일에 저장

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
# Log::init(Log::MESSAGE_ECHO);
# Log::init(Log::MESSAGE_FILE, 'log.txt');

# 출력하고자 하는 디버그 타입 설정
# default 값 : 'i','d','v','w','e'
Log::setDebugs('i','v','e');

# 메세지 추가 옵션 출력 여부 설정
# 기본값 전체 true
/*
    Log::options([
        'datetime'   => true, # 날짜시간 출력여부
        'debug_type' => true, # 디버그 타입 출력여부
        'newline'    => true  # 개행문자 출력여부
    ]);
*/

# 메시지 단일 출력
Log::i($_SERVER['REMOTE_ADDR']);
/** 
 *  2022-10-18 16:46:28 >> I : 192.168.0.1 | 
 */

# 멀티 메시지 출력
Log::i($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_METHOD'], $uri);
/** 
 * 2022-10-18 16:46:28 >> I : 192.168.0.1 | GET | /index.php/user/2 
 */

?>
