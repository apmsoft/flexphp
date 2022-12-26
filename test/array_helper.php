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
		"lowPrice": 1,
		"productName": "A",
		"purchaseCnt": 0
	},
	{
		"rank": 2,
		"lowPrice": 10,
		"productName": "B",
		"purchaseCnt": 0
	},
	{
		"rank": 3,
		"lowPrice": 101,
		"productName": "C",
		"purchaseCnt": 0
	},
	{
		"rank": 4,
		"lowPrice": 2,
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
$arrays_asc = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','ASC')->value;
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
// $arrays_desc = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->value;
// foreach($arrays_desc as $desc)
// {
//     Log::d(
//         $desc['rank'], 
//         (new TextUtil( $desc['lowPrice'] ))->numberf(',')->value, 
//         $desc['productName']
//     );
// }

// Log::d ("=================================");

// # 첫번째로 발견된 배열 받기
// $find_first_args = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->find("productName","I")->value;
// Log::d('find_first_args ', $find_first_args);

// # find index
// $find_index = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->findIndex("lowPrice",27200);
// Log::d('find_index ', $find_index);

// # 검색에 해당하는 전체 배열 받기
// $find_all = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->findAll("lowPrice",27200)->value;
// Log::d('find_all ', $find_all);

// Log::d ("=================================");

# 배열 끝에 추가
// $append_args = [
// 	"rank"=> 10,
// 	"lowPrice"=> 22000,
// 	"productName"=> "J",
// 	"purchaseCnt"=> 0
// ];

// $append = (new \Flex\Annona\Array\ArrayHelper( $args ))->append( $append_args )->value;
// Log::d('append ', $append);


// // # 배열 끝에 추가 후 ---> 배열 정렬 ASC
// $append_and_sorting_asc = (new \Flex\Annona\Array\ArrayHelper( $args ))->append($append_args)->sorting('lowPrice','ASC')->value;
// Log::d('append_and_sorting_asc ', $append_and_sorting_asc);

// Log::d ("=================================");

// # 값이 [숫자]로 되어 있는 키의 총합을 구한다 (SUM)
// $numeric_sum = (new \Flex\Annona\Array\ArrayHelper( $args ))->sum('lowPrice');
// Log::d('numeric_sum ', '총합  ', $numeric_sum);

// # 값이 [숫자]로 되어 있는 키의 평균을 구한다 (AVG)
// $numeric_avg = (new \Flex\Annona\Array\ArrayHelper( $args ))->avg('lowPrice');
// Log::d('numeric_avg ', '평균값', $numeric_avg);
?>