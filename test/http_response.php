<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Http\HttpResponse;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

$output = json_encode(["result"=>"true","msg"=>"ok!!"],JSON_UNESCAPED_UNICODE);
// Log::v($output);

# Response
echo new HttpResponse(200,[
    'Content-Type' => 'application/json; charset=utf-8'
],$output);
?>