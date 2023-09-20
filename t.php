<?php
use React\EventLoop\Loop;
use React\Promise\Promise;

use Spatie\Async\Pool;
use Flex\Annona\Log;

$path = __DIR__;
require $path.'/vendor/autoload.php';
require $path.'/config/config.inc.php';

$pool = Pool::create();

$tokenfn = function() {
    return (new \Flex\Annona\Token\TokenGenerateAtype( null,10 ))->value;
};


# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);

React\Async\parallel([
    function () {
        return new Promise(function ($resolve) {
            Loop::addTimer(1, function () use ($resolve) {
                $resolve('Slept for a whole second');
            });
        });
    },
    function () {
        return new Promise(function ($resolve) {
            Loop::addTimer(1, function () use ($resolve) {
                $resolve('Slept for another whole second');
            });
        });
    },
    function () {
        return new Promise(function ($resolve) {
            Loop::addTimer(1, function () use ($resolve) {
                $resolve('Slept for yet another whole second');
            });
        });
    },
])->then(function (array $results) use ($pool, $tokenfn) {
    foreach ($results as $result) {
        var_dump($result);
    }

    for($i = 1; $i <= 10; $i++) {
        $pool->add(function() use ($i, $tokenfn) {
            try{
                $random_moduleid = $tokenfn;
                // Log::d('랜덤 ',$random_moduleid);
                $file_name = _DATA_."/file_$i.txt";
                $content = bin2hex(random_bytes(2048));
                file_put_contents($file_name, $content);
                return $file_name;
            }catch(\Exception $e){
                Log::e('>>>>>>>',$e->getMessage());
            }
        })->then(function ($file_name) {
            echo "Generated file: $file_name".PHP_EOL;
        })->catch(function($e) {
            echo "Caught Exception ". $e->getMessage() . PHP_EOL;
        })->timeout(function() {
            echo "Process took too long \n";
        });
    }
    $pool->wait();
}, function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});
?>