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
    $imageGD = new \Flex\Image\ImageGD($model->picture);
    echo 'gd버전: '.$imageGD->getVersion().'<br />';

    # 사진 이미지 사이즈
    $img_info = $imageGD->getImageSize();
    echo '원본 사진크기 : '.$img_info->width.' x '.$img_info->height.'<br />';

    # 썸네일 이미지 만들기
    echo '썸네일 이미지 만들기 120x120<br />';
    $imageGD->thumbnailImage(120,120);
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/thumb.jpg');
    echo '<img src="/testdirectory/thumb.jpg" /><br />';

    # 이미지 자르기
    echo '이미지 자르기 500x150,x:150,y:100<br />';
    $imageGD->cropImage(500,150,150,100);
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/crop.jpg');
    echo '<img src="/testdirectory/crop.jpg" /><br />';

    # 이미지 자르기 썸네일
    echo '이미지 자르기 썸네일 120x120<br />';
    $imageGD->cropThumbnailImage(120,120);
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/cropthumb.jpg');
    echo '<img src="/testdirectory/cropthumb.jpg" /><br />';

    # 필터 워터마크 찍기
    echo '필터 워터마크 찍기<br />';
    $imageGD = new ImageGD($_SERVER['DOCUMENT_ROOT'].'/testdirectory/crop.jpg');
    $imageGD->filterWatermarks($_SERVER['DOCUMENT_ROOT'].'/testdirectory/thumb.jpg');
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/watermark.jpg');
    echo '<img src="/testdirectory/watermark.jpg" /><br />';

    echo '타이틀 이미지 만들기<br />';
    $imageGD = new ImageGD();
    $imageGD->setBgColor(0x7fffffff);
    $imageGD->setFont($_SERVER['DOCUMENT_ROOT'].'/HYSUPM.TTF');
    $imageGD->setFontColor(array(0,0,0));
    $imageGD->setFontSize(20);
    $imageGD->setXY(5,40);
    $imageGD->writeTextImage(500,60,'김형오 의장, 설 앞두고 용산노인복지관');
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/textimage.png');
    echo '<img src="/testdirectory/textimage.png" /><br />';


    echo '이미지 위에 글씨 넣기<br />';
    $imageGD = new ImageGD($_SERVER['DOCUMENT_ROOT'].'/testdirectory/watermark.jpg');
    $imageGD->setFont($_SERVER['DOCUMENT_ROOT'].'/HYSUPM.TTF');
    $imageGD->setFontColor(array(255,255,255));
    $imageGD->setFontSize(20);
    $imageGD->setXY(5,40);
    $imageGD->combineImageText(500,60,'김형오 의장, 설 앞두고 용산노인복지관');
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/combineimagetext.png');
    echo '<img src="/testdirectory/combineimagetext.png" /><br />';


    echo '그림자 텍스트 이미지<br />';
    $imageGD = new ImageGD();
    $imageGD->setFont($_SERVER['DOCUMENT_ROOT'].'/HYMJRE.TTF');
    $imageGD->setFontSize(20);
    $imageGD->setXY(5,40);
    $imageGD->writeShadowText(500,60,'연중돌봄학교로 ‘제2의 개교’ 맞는 고창성송초');
    $imageGD->write($_SERVER['DOCUMENT_ROOT'].'/testdirectory/shadowtext.png');
    echo '<img src="/testdirectory/shadowtext.png" /><br />';

}catch(Exception $e){
    echo $e->getMessage();
}

?>