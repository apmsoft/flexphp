<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

ini_set('memory_limit','-1');

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\File\Upload;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_FILE, _ROOT_PATH_.'/'._DATA_.'/log.txt');

Log::d('_FILES',$_FILES);

#@ process : 파일업로드 명
#@ filterExtension : 허용 파일 확장자
#@ filterSize : 허용 최대 파일 사이즈 (단위 :M);
#@ makeDirs : 업로드하고자 하는 디렉토리 확인 및 없을경우 자동 생성
#@ save : 파일복사
#@ filterOrientation : JPEG 사진 회원 바로 잡기
#@ fetch : 업로드 파일 경로 배열로 돌려받기

$upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/Upload';
try{
    $upload_file_info = (new Upload( $upload_dir ))
        ->process( $upload_name = 'filepond' )
        ->filterExtension(['jpg','jpeg','png','gif','pdf','hwp'])
        ->filterSize( $maxfilesize = 8 )
        ->makeDirs()
        ->save()
        ->filterOrientation()
        ->fetch();

    Log::d($upload_file_info);
}catch (\Exception $e) {
    Log::e($e->getMessage());
    Log::e(R::$sysmsg[R::$language][$e->getMessage()]);
}
?>