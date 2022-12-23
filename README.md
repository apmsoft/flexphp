# flexphp
플랙스php
서버 사이드 프로그래밍 언어 


# template 파일 및 첨부파일 업로드 루트 폴더
_data 

# chmod -R 707 _data




# [기본 버전과 Annona (설탕 사과) 사용 방법]

1. classes 폴더에 flex\annona 폴더 붙여넣기
2. config.inc.php 업데이트 하기

[덮어쓰기]
# 클래스 자동 인클루드 /--------------
spl_autoload_register(function($class_name){
    $paths =explode('\\',preg_replace('/([a-z0-9])([A-Z.])/',"$1$2",$class_name));
    if(!strcmp($paths[0],'Flex')){
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

[추가]
# 기본 선언 클래스 /-------------------
[Annona 버전]
Flex\Annona\App::init();

[기존버전]
Flex\App\Flex\Annona\App::init();
define('_LANG_',(isset($_SESSION['nation']))? $_SESSION['nation']: Flex\App\Flex\Annona\App::$lang);
define('_SITE_HOST_',Flex\App\Flex\Annona\App::$host);  # HOST URL

# resource XML 자동 로드 /---------------
[기존버전]
Flex\R\R::init();
Flex\R\R::__autoload_resource(array(
    _VALUES_  => array('sysmsg','strings','integers')
));

[Annona 버전]
Flex\Annona\R::init(Flex\Annona\App::$language);
Flex\Annona\R::__autoload_resource([
    _VALUES_  => ['sysmsg','strings','integers']
]);


