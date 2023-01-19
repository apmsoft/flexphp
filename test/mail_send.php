<?php
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Mail\MailSend;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_FILE, _ROOT_PATH_.'/'._DATA_.'/log.txt');

# 메일보내기
try{
    $mailSend = new MailSend();
    $mailSend->setTo( '테스트님',  'test@naver.com');
    $mailSend->setFrom( 'master@ddd.com', 'flexphp');
    $mailSend->setTextPlain( "임시비밀번호 ㅇㅇㅇ" );
    $mailSend->send( "비밀번호 찾기" );
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>