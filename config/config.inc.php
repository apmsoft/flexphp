<?php
use Flex\Annona\R;
use Flex\Annona\App;

# $path 경로 설정 필요 및 설정
define('_ROOT_PATH_',dirname(__DIR__));

# 기본 설정
define('_LIBS_','libs');            #PHP 외부라이브러리

# 리소스
define('_RES_','res');
define('_QUERY_','res/query');       #테이블명 및 쿼리문
define('_VALUES_','res/values');     #데이터 타입이 확실한
define('_RAW_','res/raw');           #가공되지 않은 원천 내용

# 데이터 업로드 및 캐슁파일
define('_DATA_','_data');           #파일업로드 및 캐슁파일 위치(707 또는 777)
define('_UPLOAD_','_data/files');   #첨부파일등

# 데이타베이스 정보
include_once _ROOT_PATH_.'/config/config.db.php';

# 기본 선언 클래스 /-------------------
App::init();

# resource JSON 자동 로드 /---------------
R::init(App::$language ?? '');
R::__autoload_resource([
    _VALUES_  => ['sysmsg','strings','integers','arrays']
]);

# 함수 자동 인클루드 /----------------
# function 디렉토리에 있어야 하며 클래스를 지원하는 함수들
$__autoload_helper_funs = ['_fn'];
foreach($__autoload_helper_funs as $fun_name){
    $tmp_fun_filename = _ROOT_PATH_.'/function/'.$fun_name.'.function.php';
    if(file_exists($tmp_fun_filename)){
        include_once $tmp_fun_filename;
    }
}
?>
