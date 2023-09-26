<?php
use Flex\Annona\Log;
use Flex\Annona\File\Storage;
use Flex\Annona\Array\ArrayHelper;

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


$fiber = new Fiber(function(array $files): array {
    $result = [];
    foreach ($files as $file) {
        $read_contents = (new Storage('/var/www/html/flexphp/'.$file, 'r'))->read();
        // Log::d( $read_contents );
        $result[] = $read_contents;
        Fiber::suspend($file);
    }

    return $result;
});

$files = [
'_data/log.txt',
'_data/test.txt',
'_data/putget.txt',
];

print "Deleting Files" . PHP_EOL;

# start
$last_file_deleted = $fiber->start($files);
$files_deleted   = 1;
$total_files       = count($files);

# primice
while (!$fiber->isTerminated()) {
    $percentage = round($files_deleted / $total_files, 2) * 100;
    printf("Deleted %s (%s%% done)." . PHP_EOL, $last_file_deleted, $percentage);
    echo $last_file_deleted = $fiber->resume();
    $files_deleted++;
}

# then 다 끝나면 호출
if ($fiber->isTerminated()) {
    $result = $fiber->getReturn();
    // Log::d($result);

    # 하나의 배열로 합치기
    $unionAll3 = (new ArrayHelper( [] ))->unionAll($result[0], $result[1], $result[2])->value;

    # 빈공백 제거
    Log::d("unionAll", array_filter($unionAll3,"trim"));
}
?>