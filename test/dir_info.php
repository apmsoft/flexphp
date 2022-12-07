<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# model
$model = new \Flex\Model\Model([]);
$model->dir = sprintf("%s/%s",_ROOT_PATH_, _DATA_);

# 하나의 디렉토리 경로 만들기
try{
    $dirInfo = new \Flex\Dir\DirInfo($model->dir);
    $dirInfo->makeDirectory( $model->dir.'/test1');
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 멀티 디렉토리 만들기
try{
    $dirInfo2 = new \Flex\Dir\DirInfo($model->dir.'/test2/m1/m2');
    $dirInfo2->makesDir();
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>