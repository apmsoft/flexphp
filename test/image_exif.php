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

# model
$model = new \Flex\Model\Model();
$model->picture = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe/g0e6b3d364_640.jpg';

try{
    $imageExif = new \Flex\Image\ImageExif( $model->picture );
    Log::d ('File',$imageExif->getFile());
    Log::d ('Computed', $imageExif->getComputed());
    Log::d ('Ifdo', $imageExif->getIfdo());
    Log::d ('Exif', $imageExif->getExif());
    Log::d ('GPS', $imageExif->getGPS());
    Log::d ('Makenote', $imageExif->getMakenote());
    Log::d ('한번에 출력 :', $imageExif->fetch());

    $image_exif_info = (new \Flex\Image\ImageExif( $model->picture ))->fetch();
    Log::d($image_exif_info);
}catch (\Exception $e){
    Log::e( $e->getMessage() );
}
?>
