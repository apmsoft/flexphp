<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;


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

# image_view.php?ef=imageadfesdfe/filename@c=9&s=sm
# 파일경로
$upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';

# 기본정보 옵션정보 /=====================================
$filename    = 'j.jpeg';                   # 파일명
$compression = R::arrays('image_compression')['c9'];      # 품질(압축)
$sizes       = R::arrays('image_size')['xs'];      # 이미지사이즈

# .................................허용 파일 확장자
$allowed_file_extension = R::arrays('image_view_extension');

# Image Viewer
try{
    $image_contents = (new ImageViewer( $upload_dir.'/'.$filename ))->setFilter( $compression, $sizes, $allowed_file_extension )->fetch();

    #$image_contents = (new ImageViewer( $upload_dir.'/'.$filename ))->setFilter( $compression, $sizes, $allowed_file_extension )->getContents();
    Log::v( $image_contents );
}catch(\Exception $e){
    Log::e($e->getMessage());
}

?>
