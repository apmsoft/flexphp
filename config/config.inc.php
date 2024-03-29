<?php
session_start();

# $path 경로 설정 필요 및 설정
define('_ROOT_PATH_',$path);

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
// include_once _ROOT_PATH_.'/config/config.db.php';

# 클래스 자동 인클루드 /--------------
spl_autoload_register(function($class_name){
    $paths =explode('\\',preg_replace('/([a-z0-9])([A-Z.])/',"$1$2",$class_name));
    if(!strcmp($paths[0],'Flex'))
    {
        $cnt = count($paths)-1;
        $cut = $cnt-1;
        $class_package   = implode('/',array_map('strtolower',array_slice($paths,1,$cut)));
        $class_path_name = sprintf("%s/%s",$class_package,$paths[$cnt]);
        if(!class_exists($class_path_name,false))
        {
            # classes 폴더
            if(file_exists(_ROOT_PATH_.'/classes/'.$class_path_name.'.class.php')){
                include_once _ROOT_PATH_.'/classes/'.$class_path_name.'.class.php';
            }
        }
    }
});

# 기본 선언 클래스 /-------------------
Flex\Annona\App::init();

# resource JSON 자동 로드 /---------------
Flex\Annona\R::init(Flex\Annona\App::$language);
Flex\Annona\R::__autoload_resource([
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

# 세션값 설정
# 웹 세션
$auth_type['service'] = [
    'id'     => 'auth_id',
    'userid' => 'auth_userid',
    'level'  => 'auth_level',
    'name'   => 'auth_name'
];

# 관리자 세션
$auth_type['topadm'] = [
    'id'     => 'topadm_id',
    'userid' => 'topadm_userid',
    'level'  => 'topadm_level',
    'name'   => 'topadm_name'
];
?>
