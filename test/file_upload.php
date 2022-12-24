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