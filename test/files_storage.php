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
$model->filename = sprintf("%s/%s/test.txt",_ROOT_PATH_, _DATA_);
$model->filename2 = sprintf("%s/%s/putget.txt",_ROOT_PATH_, _DATA_);
$model->filename3 = sprintf("%s/%s/csvfile.csv",_ROOT_PATH_, _DATA_);

$model->contents = 'Hello'."\n".'FlexPHP ver2.0';
$model->pg_contents = 'PUT'."\n".'GET';

$model->csv_contents = [];
$model->csv_contents = ['title','datetime','name'];
$model->csv_contents = ['의적',date('Y-m-d H:i:s'),'홍길동'];
$model->csv_contents = ['애국자',date('Y-m-d H:i:s'),'유관순'];


Log::d($model->fetch());

# 파일 쓰기 1
$filesStorage1 = new \Flex\Files\FilesStorage($model->filename, 'w');
$filesStorage1->write($model->contents);

# 파일 읽기 1
$filesStorage2 = new \Flex\Files\FilesStorage($model->filename, 'r');
Log::d($filesStorage2->read());

# 파일 쓰기 2 : 한번에 쓰기
$filesStorage3 = new \Flex\Files\FilesStorage($model->filename2, 'w');
$filesStorage3->put($model->pg_contents);

# 파일 읽기 2 : 한번에 읽기
$filesStorage4 = new \Flex\Files\FilesStorage($model->filename2, 'r');
Log::d($filesStorage4->get());

# 파일 쓰기 3 : CSV
$filesStorage5 = new \Flex\Files\FilesStorage($model->filename3, 'w');
$filesStorage5->write_csv($model->csv_contents);

# 파일 읽기 3 : CSV
$filesStorage6 = new \Flex\Files\FilesStorage($model->filename3, 'r');
Log::d($filesStorage6->read_csv());
?>
