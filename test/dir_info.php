<?php
use Flex\Annona\App;
use Flex\Annona\Log;

use Flex\Annona\Dir\DirInfo;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# model
$model = new \Flex\Annona\Model([]);
$model->dir = sprintf("%s/%s",_ROOT_PATH_, _DATA_);

Log::d($model->dir);

# 하나의 디렉토리 경로 만들기
try{
    # multi line
    $dirInfo = new DirInfo($model->dir);
    $dirInfo->makeDirectory( $model->dir.'/test1');

    # single line
    (new DirInfo($model->dir))->makeDirectory( $model->dir.'/test2');
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 멀티 디렉토리 만들기
try{
    # multi line
    $dirInfo2 = new DirInfo($model->dir.'/test1/m1/m2');
    $dirInfo2->makesDir();

    # single line
    (new DirInfo($model->dir.'/test2/m1/m2'))->makesDir();
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 디렉토리 탐색
try{
    $find_dirs = (new \Flex\Annona\Dir\DirObject($model->dir))->findFolders();
    Log::d ( $find_dirs );
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>
