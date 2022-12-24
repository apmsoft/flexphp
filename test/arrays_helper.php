<?php
use Flex\Annona\Log;
use Flex\Annona\Text\TextUtil;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


$data = '[
	{
		"rank": 1,
		"lowPrice": 25000,
		"productName": "A",
		"purchaseCnt": 0
	},
	{
		"rank": 2,
		"lowPrice": 26000,
		"productName": "B",
		"purchaseCnt": 0
	},
	{
		"rank": 3,
		"lowPrice": 79900,
		"productName": "C",
		"purchaseCnt": 0
	},
	{
		"rank": 4,
		"lowPrice": 24000,
		"productName": "D",
		"purchaseCnt": 0
	},
	{
		"rank": 5,
		"lowPrice": 27200,
		"productName": "E",
		"purchaseCnt": 0
	},
	{
		"rank": 6,
		"lowPrice": 31000,
		"productName": "F",
		"purchaseCnt": 0
	},
	{
		"rank": 7,
		"lowPrice": 34120,
		"productName": "G",
		"purchaseCnt": 0
	},
	{
		"rank": 8,
		"lowPrice": 27200,
		"productName": "H",
		"purchaseCnt": 0
	},
	{
		"rank": 9,
		"lowPrice": 27200,
		"productName": "I",
		"purchaseCnt": 0
	}
]';

# array data
$args = json_decode($data,true);

# ASC 
$arrays_asc = (new \Flex\Annona\Arrays\ArraysHelper( $args ))->multiSort('lowPrice','ASC')->value;
foreach($arrays_asc as $asc)
{
    Log::d( 
        $asc['rank'], 
        (new TextUtil( $asc['lowPrice'] ))->numberf(',')->value, 
        $asc['productName']
    );
}

Log::d ("=================================");

# DESC 
$arrays_desc = (new \Flex\Annona\Arrays\ArraysHelper( $args ))->multiSort('lowPrice','DESC')->value;
foreach($arrays_desc as $desc)
{
    Log::d(
        $desc['rank'], 
        (new TextUtil( $desc['lowPrice'] ))->numberf(',')->value, 
        $desc['productName']
    );
}
?>