<?php
use Flex\App\App;
use Flex\Log\Log;
use Flex\R\R;

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
Log::d(R::$r->imageviewer[R::$language]);

# model
$model = new \Flex\Model\Model( R::$r->imageviewer[R::$language] );

# image_view.php?ef=imageadfesdfe/filename@c=9&s=sm
# 파일경로
$upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';

# 기본정보
$filename    = 'j.jpeg';        # 파일명
$compression = $model->c9;      # 품질(압축)
$sizes       = $model->sm;      # 이미지사이즈

# 허용 파일 확장자
$allowed_file_extension = explode(',',$model->file_extension);

# Image Viewer
$imageViewer = new \Flex\Image\ImageViewer( $upload_dir );
$images = $imageViewer->doView( $filename, $compression, $sizes, $allowed_file_extension);
Log::v( $images );
?>
