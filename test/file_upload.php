<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

ini_set('memory_limit','-1');

use Flex\App\App;
use Flex\Log\Log;
use FLex\R\R;

use Flex\File\FileUpload;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init(Log::MESSAGE_FILE, _ROOT_PATH_.'/'._DATA_.'/log.txt');
Log::d($_FILES);
$upload_dir = _ROOT_PATH_.'/'._UPLOAD_.'/fileupload';
try{
    $upload_file_info = (new FileUpload( $upload_dir ))
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