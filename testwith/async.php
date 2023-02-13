<?php
use React\EventLoop\Loop;
use Spatie\Async\Pool;

use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

$path = dirname(__DIR__);
require $path. '/vendor/autoload.php';
require $path. '/config/config.inc.php';

Log::init(Log::MESSAGE_ECHO);
Log::options([
    'datetime'   => false, # 날짜시간 출력여부
    'debug_type' => false, # 디버그 타입 출력여부
    'newline'    => false  # 개행문자 출력여부
]);

$loop = React\EventLoop\Loop::get();
$stime = microtime(true);

$dir = sprintf("%s/%s/t",_ROOT_PATH_, _DATA_);



$pool = Pool::create();
for($i = 1; $i <= 10000; $i++) 
{
    $pool->add(function() use ($i, $dir)
    {
        $file_name = "file_$i.txt";
        $content = bin2hex(random_bytes(2048));
        file_put_contents($dir.'/'.$file_name, $content);

        return $file_name;
    })->then(function ($file_name) {
        Log::d("Generated file: $file_name");
    });
}

for($j =0; $j <= 10000; $j++){
    Log::d($j);
}

$pool->wait();


$loop->run();

$microtime = microtime(true) - $stime;
$duration = convert2MTDT($microtime);
echo ">>>>>> finish ".$duration .PHP_EOL;
?>