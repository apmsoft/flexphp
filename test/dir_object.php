<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# model
$model = new \Flex\Annona\Model\Model([]);
$model->dir  = sprintf("%s",_ROOT_PATH_);
$model->dir2 = sprintf("%s/classes/db",_ROOT_PATH_);

# 디렉토리 탐색
try{
    $find_files = (new \Flex\Annona\Dir\DirObject($model->dir))->findFolders();
    Log::d ( $find_files );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 디렉토리 탐색 [제외시킬 디렉토리명]
try{
    $find_files = (new \Flex\Annona\Dir\DirObject($model->dir))->findFolders(['vendor']);
    Log::d ( $find_files );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 파일 탐색
try{
    $find_files = (new \Flex\Annona\Dir\DirObject($model->dir2))->findFiles();
    Log::d ( $find_files );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 파일 탐색  원하는 파일만
try{
    $find_files = (new \Flex\Annona\Dir\DirObject($model->dir))->findFiles('*.json');
    Log::d ( $find_files);
}catch(\Exception $e){
    Log::e($e->getMessage());
}

# 제외시킬 파일 확장자
try{
    $find_files = (new \Flex\Annona\Dir\DirObject($model->dir))->findFiles('*', ['json','md']);
    Log::d ( $find_files );
}catch(\Exception $e){
    Log::e($e->getMessage());
}
?>
