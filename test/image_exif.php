<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;
use Flex\Annona\R\R;

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
$model = new \Flex\Annona\Model\Model();
$model->picture = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe/beautiful-young-asian-woman-happy-relax-walking-beach-near-sea.jpg';

try{
    $imageExif = new \Flex\Annona\Image\ImageExif( $model->picture );
    Log::d ('File',$imageExif->getFile());
    Log::d ('Computed', $imageExif->getComputed());
    Log::d ('Ifdo', $imageExif->getIfdo());
    Log::d ('Exif', $imageExif->getExif());
    Log::d ('GPS', $imageExif->getGPS());
    Log::d ('Makenote', $imageExif->getMakenote());
    Log::d ('한번에 출력 :', $imageExif->fetch());

    $image_exif_info = (new \Flex\Annona\Image\ImageExif( $model->picture ))->fetch();
    Log::d($image_exif_info);
}catch (\Exception $e){
    Log::e( $e->getMessage() );
}
?>
