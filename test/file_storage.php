<?php
use Flex\App\App;
use Flex\Log\Log;


use Flex\File\FileStorage;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# model
$model = new \Flex\Model\Model([]);
$model->filename = sprintf("%s/%s/test.txt",_ROOT_PATH_, _DATA_);
$model->filename2 = sprintf("%s/%s/putget.txt",_ROOT_PATH_, _DATA_);
$model->filename3 = sprintf("%s/%s/csvfile.csv",_ROOT_PATH_, _DATA_);

$model->contents = 'Hello'."\n".'FlexPHP ver2.0';
$model->pg_contents = 'PUT'."\n".'GET';

$model->csv_contents = [];
$model->{"csv_contents+"} = ['title','datetime','name'];
$model->{"csv_contents+"} = ['의적',date('Y-m-d H:i:s'),'홍길동'];
$model->{"csv_contents+"} = ['애국자',date('Y-m-d H:i:s'),'유관순'];


Log::d($model->fetch());

# SplFileObject 상속합니다
# https://www.php.net/manual/en/class.splfileobject.php 

# 파일 쓰기 1
(new FileStorage($model->filename, 'w'))->write($model->contents);

# 파일 읽기 1
$read_contents = (new FileStorage($model->filename, 'r'))->read();
Log::d( $read_contents );

# 파일 쓰기 2 : 한번에 쓰기
(new FileStorage($model->filename2, 'w'))->put($model->pg_contents);

# 파일 읽기 2 : 한번에 읽기
$get_contents = (new FileStorage($model->filename2, 'r'))->get();
Log::d($get_contents);

# 파일 쓰기 3 : CSV
(new FileStorage($model->filename3, 'w'))->write_csv($model->csv_contents);

# 파일 읽기 3 : CSV
$read_csv = (new FileStorage($model->filename3, 'r'))->read_csv();
Log::d( $read_csv );
?>
