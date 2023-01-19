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
$model->picture = $model->dir.'/thumb90100x100_j.jpeg';

# 기본 멀티 코딩
try{
    $fileDownload = new Download( $model->picture );
    
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

    // Log::d('file 확장자', $file_extension);
    // Log::d('file bytes', $bytes);
    // Log::d('file size', $size);
    // Log::d('file contents', $file_contents);
    
    # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨)
    // $fileDownload->download(sprintf("테스트.%s",$fileDownload->file_extension));


    #===========[한줄코딩]============;
    # 파일 컨테츠만 가져오기;
    $file_contents = (new Download( $model->picture ))->getContents();
    Log::d($file_contents);

    # 허용된 확장자만
    // $file_contents = (new Download( $model->picture ))->setFileTypes(['pdf'])->getContents();
    #Log::d('file contents', $file_contents);
    
    # 다운로드 실행 # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨);
    // (new Download( $model->picture ))->download('테스트');
    // (new Download( $model->picture ))->setFileTypes(['csv','jpeg'])->download('테스트.jpeg');
}catch (\ErrorException $e){
    Log::e($e->getMessage());
    Log::e(R::$sysmsg[R::$language][$e->getMessage()]);
}
?>