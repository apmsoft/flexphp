<?php
use Flex\App\App;
use Flex\Log\Log;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);
Log::setDebugs('d','e');

# model
$model = new \Flex\Model\Model();
$model->dir  = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';
$model->pattern = "gd_*.png"; # 삭제할 파일 패턴

try{
    # 삭제할 파일 찾아보기
    $find_files = (new \Flex\File\FileRemove( $model->dir ))->find( $model->pattern )->files;
    Log::d('find files ', $find_files);

    # 파일삭제
    // (new \Flex\File\FileRemove( $model->dir ))->find( $model->pattern )->remove();
}catch (\ErrorException $e){
    Log::e($e->getMessage());
}
?>