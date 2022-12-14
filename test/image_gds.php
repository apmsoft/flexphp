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
$model->dir  = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';
$model->picture = $model->dir.'/g0e6b3d364_640.jpg';

try{
    $imageGDS = new \Flex\Image\ImageGDS($model->picture);
    Log::d( 'gd버전: ', $imageGDS->getVersion() );

    # 사진 이미지 사이즈
    $img_info = $imageGDS->getImageSize();
    Log::d('원본 사진크기 : ', 'width : '.$img_info->width, 'height : '.$img_info->height);

    # 썸네일 이미지 만들기
    Log::d('썸네일 이미지 만들기 120x120');
    $imageGDS->thumbnailImage(120,120);
    $imageGDS->write($model->dir."/gd_thumb.jpg");
    Log::d($model->dir."/gd_thumb.jpg");

    # 이미지 자르기
    Log::d('이미지 자르기 500x150,x:150,y:100');
    $imageGDS->cropImage(500,150,100,100);
    $imageGDS->write($model->dir.'/gd_crop_500x150_x150_y100.jpg');
    Log::d($model->dir.'/gd_crop_500x150x150y100.jpg');

    # 이미지 자르기 썸네일
    Log::d('이미지 자르기 썸네일 120x120');
    $imageGDS->cropThumbnailImage(120,100);
    $imageGDS->write($model->dir.'/gd_cropthumb_120x100.jpg');
    Log::d($model->dir.'/gd_cropthumb_120x100.jpg');

    # 필터 워터마크 찍기
    # RB : 오른쪽 아래 기분, LB : 왼쪽 아래 기분, LT : 왼쪽 위 기준, RT : 오른쪽 위 기준
    Log::d('필터 워터마크 찍기');
    $imageGDS = new \Flex\Image\ImageGDS($model->dir.'/gd_crop_500x150_x150_y100.jpg');
    // $imageGDS->filterWatermarks($model->dir.'/thumb90100x100_j.jpeg', 5, 5, 'RB');
    // $imageGDS->filterWatermarks($model->dir.'/thumb90100x100_j.jpeg', 5, 5, 'LB');
    // $imageGDS->filterWatermarks($model->dir.'/thumb90100x100_j.jpeg', 5, 5, 'LT');
    $imageGDS->filterWatermarks($model->dir.'/thumb90100x100_j.jpeg', 5, 5, 'RT');
    $imageGDS->write($model->dir.'/gd_watermarks.jpg');
    Log::d($model->dir.'/gd_watermarks.jpg');

    Log::d('타이틀 이미지 만들기');
    $imageGDS = new \Flex\Image\ImageGDS();
    $imageGDS->setBgColor(0x7fffffff);
    $imageGDS->setFont(_ROOT_PATH_.'/fonts/NanumMyeongjo-YetHangul.ttf');
    $imageGDS->setFontColor([0,0,0]);
    $imageGDS->setFontSize(20);
    $imageGDS->setXY(5,40);
    $imageGDS->writeTextImage(500,60,'김형오 의장, 설 앞두고 용산노인복지관');
    $imageGDS->write($model->dir.'/gd_title_image.png');
    Log::d($model->dir.'/gd_title_image.png');


    Log::d('이미지 위에 글씨 넣기');
    $imageGDS = new \Flex\Image\ImageGDS($model->dir.'/gd_watermarks.jpg');
    $imageGDS->setFont(_ROOT_PATH_.'/fonts/NanumMyeongjo-YetHangul.ttf');
    $imageGDS->setFontColor([255,255,255]);
    $imageGDS->setFontSize(20);
    $imageGDS->setXY(30,120); // 위치
    $imageGDS->combineImageText(500,150, '제니는 너무 예뻐!!');
    $imageGDS->write($model->dir.'/gd_combine_image_text.png');
    Log::d($model->dir.'/gd_combine_image_text.png');


    Log::d('그림자 텍스트 이미지');
    $imageGDS = new \Flex\Image\ImageGDS();
    $imageGDS->setFont(_ROOT_PATH_.'/fonts/NanumMyeongjo-YetHangul.ttf');
    $imageGDS->setFontSize(20);
    $imageGDS->setXY(5,50);
    $imageGDS->writeShadowText(500,60,'연중돌봄학교로 ‘제2의 개교’ 맞는 고창성송초');
    $imageGDS->write($model->dir.'/gd_shadowtext.png');
    Log::d($model->dir.'/gd_shadowtext.png');

}catch(Exception $e){
    Log::e($e->getMessage());
}

?>