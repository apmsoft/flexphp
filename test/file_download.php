<?php
use Flex\Annona\App\App;
use Flex\Annona\Log\Log;
use Flex\Annona\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);
Log::setDebugs('d','e');

# model
$model = new \Flex\Annona\Model\Model();
$model->dir  = _ROOT_PATH_.'/'._UPLOAD_.'/imageadfesdfe';
$model->picture = $model->dir.'/thumb90100x100_j.jpeg';

# 기본 멀티 코딩
try{
    $fileDownload = new \Flex\Annona\File\FileDownload( $model->picture );
    $file_extension = $fileDownload->file_extension;
    $bytes          = $fileDownload->bytes();
    $size           = $fileDownload->size();

    # 다운로드 허용 파일 확장자 등록
    // $fileDownload->setFileTypes(
    //     ['pdf','xls','xlsx','doc','docx','zip','hwp','ppt','pptx','jpg','jpeg','png','gif']
    // );

    # 파일 컨텐츠만 가져오기
    $file_contents  = $fileDownload->getContents();
    
    # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨)
    // $fileDownload->download('테스트');

    Log::d('file 확장자', $file_extension);
    Log::d('file bytes', $bytes);
    Log::d('file size', $size);
    Log::d('file contents', $file_contents);


    #===========[한줄코딩]============;
    # 파일 컨테츠만 가져오기;
    // $file_contents = (new \Flex\Annona\File\FileDownload( $model->picture ))->getContents();

    # 허용된 확장자만
    // $file_contents = (new \Flex\Annona\File\FileDownload( $model->picture ))->setFileTypes(['pdf'])->getContents();
    #Log::d('file contents', $file_contents);
    
    # 다운로드 실행 # 파일 다운로드 실행 (이전에 어떠한 문자도 출력이 되어선 안됨);
    // (new \Flex\Annona\File\FileDownload( $model->picture ))->download('테스트');
    // (new \Flex\Annona\File\FileDownload( $model->picture ))->setFileTypes(['pdf'])->download('테스트');
}catch (\ErrorException $e){
    Log::e($e->getMessage());
    Log::e(R::$sysmsg[R::$language][$e->getMessage()]);
}
?>