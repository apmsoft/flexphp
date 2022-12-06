<?php
use Flex\App\App;
use Flex\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);
Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => true, # 디버그 타입 출력여부
    'newline'    => true  # 개행문자 출력여부
]);

# 기본값 

# 1
$model = new \Flex\Model\Model( [] );
$model->name = '홍길동';
$model->age = 11;

# model 데이터 전체 
Log::d( $model->fetch() );

# iseet
if(isset($model->name)){
    Log::d( 'model name  : true' , $model->name);
}

# unset
unset($model->name);
if(!isset($model->namne)){
    Log::d( 'model name  : unset ------>');
}

Log::d( $model->fetch() );
Log::d("===============");

# 배열 예제1 
# 선언된 값이 배열일 경우 자동 순번 array_push /=============
$model->list = [];
$weeks = ['일','월','화'];
foreach($weeks as $idx => $week_title)
{
    $model->{"list+"} = [
        'no' => $idx,
        'title' => $week_title,
        'signdate' => date('Y-m-d H:i:s')
    ];
}
Log::d( $model->list );
Log::d( $model->list[0]);
Log::d("===============");

# 배열 예제 2
$model->args = [
    'name' => '홍길동',
    'age' => 20
];
Log::d( $model->args );
Log::d('name : ', $model->args['name']);

# args name키에 해당하는 값을 바꾼다
Log::d('name :홍길동 -> name :  유관순으로 바꾸기');
$model->{"args{name}"} = '유관순';

Log::d($model->args);
Log::d('name : ', $model->args['name']);
Log::d("===============");



# index array 로 되어 있는 값
$model->loop = [];
$model->{"loop+"} = ['no'=>1, 'title'=>'t1'];
$model->{"loop+"} = ['no'=>2, 'title'=>'t2'];

Log::d( $model->loop );

# loop 문 index 에 해당하는 값 바꾸기
Log::d('loop 문 index 에 해당하는 값 바꾸기');
$model->{"loop{0}{title}"} = 'title1';

Log::d( $model->loop );
Log::d( $model->loop[0] );
Log::d("===============");





# 배열 예제3 스트링 배열 자동 증가
$model->input = [];
$model->{"input+"} = 'a';
$model->{"input+"} = 'b';
$model->{"input+"} = 'c';
$model->{"input+"} = 'd';
$model->{"input+"} = 'e';
$model->{"input+"} = 'f';
$model->{"input+"} = 'g';

Log::d( $model->input );


# 배열 뒤에서 하나 뺴기
Log::d('배열 뒤에서 하나 뺴기 : input-');
$model->{"input-"} = '';
Log::d( $model->input );

# 배열 index 번호 부터 끝까지
Log::d('배열 index 번호 2부터 끝까지 : input-{2}');
$model->{"input-{2}"} = '';
Log::d( $model->input );

# 뒤에서부터 3개만
Log::d('뒤에서부터 -3개만 : input-{-3}');
$model->{"input-{-3}"} = '';
Log::d( $model->input );

# 시작자릿수 - 갯수(몇개)
Log::d('시작자릿수[0] - 갯수(2) : input-{0}{2}');
$model->{"input-{0}{2}"} = '';
Log::d( $model->input );
?>
