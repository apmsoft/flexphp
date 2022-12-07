<?php
session_start();

use Flex\R\R;
use Flex\App\App;

// @ini_set('include_path', './PEAR' . PATH_SEPARATOR .?; ini_get('include_path'));
// @ini_set('display_error', 'On');
# set error reporting
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
#error_reporting(E_ALL & ~E_NOTICE);
#error_reporting(E_ALL);

# $path 경로 설정 필요 및 설정
define('_ROOT_PATH_',$path);
define('_CHRSET_','utf-8');

# 기본 설정
define('_SRC_','src');              #PHP 파일 프로그램 폴더
define('_LIBS_','libs');            #PHP 외부라이브러리

# 리소스
define('_RES_','res');
define('_CONFIG_',_RES_.'/config');     #설정
define('_QUERY_',_RES_.'/query');       #테이블명 및 쿼리문
define('_VALUES_',_RES_.'/values');     #데이터 타입이 확실한
define('_RAW_',_RES_.'/raw');           #가공되지 않은 원천 내용

# 데이터 업로드 및 캐슁파일
define('_DATA_','_data');           #파일업로드 및 캐슁파일 위치(707 또는 777)
define('_UPLOAD_','_data'.'/files');   #첨부파일등

# 기본 전체관리자 등급(level)
define('_AUTH_SUPERADMIN_LEVEL', 100);
define('_AUTH_SUPERDEVEL_LEVEL', 999);

# 어플리케이션과 연동용
define('_AUTHTOKEN_', '');

# 데이타베이스 정보
include_once _ROOT_PATH_.'/config/config.db.php';

# 클래스 자동 인클루드 /--------------
spl_autoload_register(function($class_name){
    $tmp_args=explode('\\',preg_replace('/([a-z0-9])([A-Z.])/',"$1$2",$class_name));
    $class_path_name= (count($tmp_args)>1) ? 
        strtolower($tmp_args[1]). '/'. $tmp_args[2] :
            strtolower($tmp_args[0]). '/'. $tmp_args[0];
    
    if(!class_exists($class_path_name,false))
    {
        # classes 폴더
        if(file_exists(_ROOT_PATH_.'/classes/'.$class_path_name.'.class.php')!==false){
            include_once _ROOT_PATH_.'/classes/'.$class_path_name.'.class.php';
        }
    }
});

# 기본 선언 클래스 /-------------------
# 어플리케이션 환경
App::init();
define('_LANG_',(isset($_SESSION['nation']))? $_SESSION['nation']: App::$lang);       # 다국어중 언어 셋팅
define('_SITE_HOST_',App::$host);  # HOST URL

# resource XML 자동 로드 /---------------
R::init();
R::__autoload_resource(array(
    _VALUES_  => array('sysmsg','strings','integers')
));

# 함수 자동 인클루드 /----------------
# function 디렉토리에 있어야 하며 클래스를 지원하는 함수들
# 파일명 규칙 (_[*].helper.php)
$__autoload_helper_funs = array(
    '_datetimes','_stringobject','_outpane','_default'
);
if(is_array($__autoload_helper_funs)){
    foreach($__autoload_helper_funs as $fun_name){
        $tmp_fun_filename = _ROOT_PATH_.'/function/'.$fun_name.'.helper.php';
        if(file_exists($tmp_fun_filename)!==false){
            include_once $tmp_fun_filename;
        }
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
