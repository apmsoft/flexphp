<?php
use Flex\Annona\App;
use Flex\Annona\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_FILE, _ROOT_PATH_.'/'._DATA_.'/log.txt');

$mailSend = new \Flex\Annona\Mail\MailSend();
$mailSend->setFrom('webmaster@fancyupsoft.com', 'test');
$mailSend->setDescription('메일전송 테스트 내용입니다.');
$mailSend->setTo('나당', 'apmsoft@gmail.com');
$mailSend->setTo('나당', 'ehomebuild@naver.com');
if($mailSend->send('메일전송 테스트')){
    Log::v('전송성공');
}else Log::e('전송 실패');
?>