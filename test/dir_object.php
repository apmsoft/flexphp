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
$model->dir  = sprintf("%s",_ROOT_PATH_);
$model->dir2 = sprintf("%s/classes/db",_ROOT_PATH_);

# 디렉토리 탐색
$dirObject = new \Flex\Dir\DirObject($model->dir);
Log::d ( $dirObject->findFolders() );

# 디렉토리 탐색 [제외시킬 디렉토리명]
$dirObject1 = new \Flex\Dir\DirObject($model->dir);
Log::d ( $dirObject1->findFolders(['vendor']) );

# 파일 탐색
$dirObject2 = new \Flex\Dir\DirObject($model->dir2);
Log::d ( $dirObject2->findFiles() );

# 파일 탐색  원하는 파일만
$dirObject3 = new \Flex\Dir\DirObject($model->dir);
Log::d ( $dirObject3->findFiles('*.json') );

# 제외시킬 파일 확장자
$dirObject4 = new \Flex\Dir\DirObject($model->dir);
Log::d ( $dirObject4->findFiles('*', ['json','md']) );
?>
