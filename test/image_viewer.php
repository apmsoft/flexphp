<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;
use Flex\Annona\R\R;


use Flex\Annona\Image\ImageViewer;

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

# resource
R::parser(_ROOT_PATH_.'/'._CONFIG_.'/imageviewer.json', 'imageviewer');
// Log::d(R::$r->imageviewer[R::$language]);

# image viewer config :: model
$imageViewOptions = new \Flex\Annona\Model\Model( R::$r->imageviewer[R::$language] );

#=============================
/**
    "file_extension": ["gif","jpg","jpeg","png"],
    "c9" : 90,
    "c8" : 80,
    "c7" : 70,
    "c6" : 60,
    "c5" : 50,
    "c4" : 40,
    "c3" : 30,
    "c2" : 20,
    "c1" : 10,
    "xs" : "100x100",
    "sm" : "293x293",
    "md" : "320x320",
    "lg" : "480x480",
    "xl" : "640x640",
    "2xl": "720x720",
    "3xl": "1024x1024",
    "4xl": "1280x1280"
 */
# image_view.php?ef=imageadfesdfe/filename@c=9&s=sm
# 파일경로
$upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';

# 기본정보 옵션정보 /=====================================
$filename    = 'j.jpeg';                   # 파일명
$compression = $imageViewOptions->c9;      # 품질(압축)
$sizes       = $imageViewOptions->xs;      # 이미지사이즈

# .................................허용 파일 확장자
$allowed_file_extension = $imageViewOptions->file_extension;

# Image Viewer
try{
    $image_contents = (new ImageViewer( $upload_dir.'/'.$filename ))->setFilter( $compression, $sizes, $allowed_file_extension )->fetch();

    #$image_contents = (new ImageViewer( $upload_dir.'/'.$filename ))->setFilter( $compression, $sizes, $allowed_file_extension )->getContents();
    Log::v( $image_contents );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

?>
