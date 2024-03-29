<?php
use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# Log setting
Log::init();
Log::init(Log::MESSAGE_ECHO);
Log::options([
    'datetime'   => true, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# ftp
try{
    $ftp = new \Flex\Annona\Ftp\Ftp(
        'dddd.com',
        "sdfsa",
        "dddd&*d",
        21,
        false
    );
    $cur_directory = $ftp->ftp_pwd();
    Log::d($cur_directory);

    $ftp->ftp_chdir('_adm');
    $cur_directory = $ftp->ftp_pwd();
    Log::d($cur_directory);

    $list = $ftp->ftp_rawlist('.');
    print_r($list);
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>
