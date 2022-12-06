<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

# 기본값 
$args = [
    'age' => 20
];

# 1
$model = new \Flex\Model\Model( $args );

# model 데이터 전체 
Log::d( $model->fetch() );

# auto set key => value
$model->name = '홍길동';

# iseet
if(isset($model->namne)){
    Log::d( 'model name  : true' );
}

# unset
unset($model->name);
if(!isset($model->namne)){
    Log::d( 'model name  : unset' );
}

# 배열 예제1 
# 선언된 값이 배열일 경우 자동 순번 array_push /=============
$model->list = [];
$weeks = ['일','월','화','수','목','금','토'];
foreach($weeks as $idx => $week_title)
{
    $model->list = [
        'no' => $idx,
        'title' => $week_title,
        'signdate' => date('Y-m-d H:i:s')
    ];
}
Log::d( $model->list );

# 배열 예제 2
$model->pre_args = [
    'name' => '홍길동',
    'age' => 20
];
Log::d( $model->pre_args );

# 자동으로 값을 추가
$model->pre_args = ['name'=>'유관순'];
$model->pre_args = ['name'=>'오솔이'];
$model->pre_args = [['aaa'=>'bbbb']];
Log::d( $model->pre_args );

# 배열 예제3 스트링 배열 자동 증가
$model->str_argv = [];
$model->str_argv = '유관순';
$model->str_argv = '홍길동';
$model->str_argv = '이순신';
$model->str_argv = '세종대왕';
Log::d( $model->str_argv );
?>
