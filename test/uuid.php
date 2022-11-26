<?php
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# class
$uuidGenerator = new Flex\Uuid\UuidGenerator();

# create
$v4 = $uuidGenerator->v4();
echo 'v4 :: '.$v4.PHP_EOL;

# uuid, hash 값이 같으면 비교 가능
$v3 = $uuidGenerator->v3('0e27f8a6-a4cf-35ac-bf84-bed31526e2c8', 'aa');
echo 'v3 :: '.$v3.PHP_EOL;

# uuid, hash 값이 같으면 비교 가능
$v5 = $uuidGenerator->v5('0e27f8a6-a4cf-35ac-bf84-bed31526e2c8', 'bb');
echo 'v5 :: '.$v5.PHP_EOL;
?>