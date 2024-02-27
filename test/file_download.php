<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\File\Download;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);
Log::setDebugs('d','e');

# model
$model = new \Flex\Annona\Model();
$model->dir  = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';
$model->filename = $model->dir.'/g0e6b3d364_640.jpg';
Log::d($model->filename);

# 기본 멀티 코딩
try{
    $fileDownload = new Download( $model->filename );
    
    # set 다운로드 허용 파일 확장자 등록
    $fileDownload->setFileTypes(
        ['pdf','xls','xlsx','doc','docx','zip','hwp','ppt','pptx','jpg','jpeg','png','gif']
    );

    # get
    $file_extension = $fileDownload->file_extension;
    $bytes          = $fileDownload->bytes();
    $size           = $fileDownload->size();

    # 파일 컨텐츠만 가져오기
    $file_contents  = $fileDownload->getContents();

    # 다운로드 헤더 값 받기
    Log::d($fileDownload->headers);

    # 다운로드 파일명 설정
    $fileDownload->setFileName(sprintf("테스트.%s",$fileDownload->file_extension));
    Log::d($fileDownload->headers);

    # 새로운 헤더 추가 및 덮어쓰기
    $fileDownload->headers = ['Cache-control' => 'public'];
    $fileDownload->headers = ['Expires' => '0'];
    $fileDownload->headers = ['Content-Length' => $fileDownload->size()];
    Log::d($fileDownload->headers);

    // Log::d('file 확장자', $file_extension);
    // Log::d('file bytes', $bytes);
    // Log::d('file size', $size);
    // Log::d('file contents', $file_contents);
    
    # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨)
    // $fileDownload->download();


    #===========[한줄코딩]============;
    # 파일 컨테츠만 가져오기;
    $file_contents = (new Download( $model->filename ))->getContents();
    // Log::d($file_contents);

    # 허용된 확장자만
    // $file_contents = (new Download( $model->picture ))->setFileTypes(['pdf'])->getContents();
    #Log::d('file contents', $file_contents);
    
    # 다운로드 실행 # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨);
    // (new Download( $model->picture ))->sefFileName('테스트.jpeg')->download();
    // (new Download( $model->picture ))->setFileTypes(['csv','jpeg'])->sefFileName('테스트.jpeg')->download();
}catch (\ErrorException $e){
    Log::e($e->getMessage());
    Log::e(R::$sysmsg[R::$language][$e->getMessage()]);
}
?>