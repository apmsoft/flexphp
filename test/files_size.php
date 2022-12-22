<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


# model
$model = new \Flex\Model\Model();
$model->dir  = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';
$model->picture = $model->dir.'/g0e6b3d364_640.jpg';

# 파일로 파일 사이즈 구하기
try{
    Log::d('파일로 파일 사이즈 구하기');
    $bytes= (new \Flex\Files\FilesSize( $model->picture ))->bytes();
    $size = (new \Flex\Files\FilesSize( $model->picture ))->size();
    Log::d('file bytes', $bytes);
    Log::d('file size', $size);
}catch (\ErrorException $e){
    Log::e($e->getMessage());
}

# 파일 사이즈 등록 및 구하기
try{
    Log::d('파일 사이즈 등록 및 구하기');
    $size = (new \Flex\Files\FilesSize( ))->setBytes(72054)->size();
    Log::d('file size', $size);
}catch (\ErrorException $e){
    Log::e($e->getMessage());
}

# 객체 선언 변수로 받아 처리
try{
    Log::d('객체 선언 변수로 받아 처리');
    $filesSize = new \Flex\Files\FilesSize( );
    $size = $filesSize->setBytes(72054)->size();
    Log::d('file size', $size);
}catch (\ErrorException $e){
    Log::e($e->getMessage());
}
?>