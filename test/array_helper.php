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
		"muid":1,
		"rank": 1,
		"lowPrice": 1000,
		"productName": "A",
		"purchaseCnt": 0
	},
	{
		"muid":1,
		"rank": 2,
		"lowPrice": 2000,
		"productName": "B",
		"purchaseCnt": 0
	},
	{
		"muid":2,
		"rank": 4,
		"lowPrice": 5000,
		"productName": "D",
		"purchaseCnt": 0
	},
	{
		"muid":2,
		"rank": 5,
		"lowPrice": 27200,
		"productName": "E",
		"purchaseCnt": 0
	},
	{
		"muid":3,
		"rank": 7,
		"lowPrice": 34120,
		"productName": "G",
		"purchaseCnt": 0
	},
	{
		"muid":3,
		"rank": 8,
		"lowPrice": 27200,
		"productName": "G",
		"purchaseCnt": 0
	}
]';

# array data
$args = json_decode($data,true);

$args2= [];
$args2[] = [
	"id" => 1,
	"name" => "홍길동",
	"userid" => "a@gmail.com",
	"cellphone" => "01011110000"
];
$args2[] = [
	"id" => 2,
	"name" => "유관순",
	"userid" => "b@gmail.com",
	"cellphone" => "01022220000"
];
$args2[] = [
	"id" => 3,
	"name" => "이순신",
	"userid" => "c@gmail.com",
	"cellphone" => "01033330000"
];

$args3 = [];
$args3[] = [
	"id"    => 1,
	"title" => "3제1",
	"name"  => "이순신1"
];
$args3[] = [
	"id"    => 2,
	"title" => "3제2",
	"name"  => "이순신2"
];
$args3[] = [
	"id"    => 3,
	"title" => "3제3",
	"name"  => "이순신3"
];

# ASC 
// $arrays_asc = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','ASC')->value;
// foreach($arrays_asc as $asc)
// {
//     Log::d( 
//         $asc['rank'], 
//         (new TextUtil( $asc['lowPrice'] ))->numberf(',')->value, 
//         $asc['productName']
//     );
// }

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

# 첫번째로 발견된 배열 받기
// $find_first_args = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->find("productName","I")->value;
// Log::d('find_first_args ', $find_first_args);

# 첫번째로 발견된 배열 index 키값 돌려받기
// $find_index = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->findIndex("lowPrice",27200);
// Log::d('find_index ', $find_index);

# 검색에 해당하는 전체 배열 받기 => (key, value)
// $find_all = (new \Flex\Annona\Array\ArrayHelper( $args ))->sorting('lowPrice','DESC')->findAll("productName","I", "E")->value;
// Log::d('find_all 싱글 값', $find_all);

# 특정키에 해당하는 여러가지 값에 해당하는 배열값 찾기 - 방법 1 => (key, values)
// $find_all = (new \Flex\Annona\Array\ArrayHelper( $args ))->findAll("productName",["I","G"])->value;
// Log::d("find all 멀티 값 방법 1",$find_all);

# 특정키에 해당하는 여러가지 값에 해당하는 배열값 찾기 - 방법 2  => (key, values)
// $find_all = (new \Flex\Annona\Array\ArrayHelper( $args ))->findAll("productName","I","G")->value;
// Log::d("find all 멀티 값 방법 2", $find_all);

# 멀티 키=>밸류에 해당하는 값 배열 모두 찾기 OR
// $find_where_all = (new \Flex\Annona\Array\ArrayHelper( $args ))->findWhere(["productName"=>'G',"lowPrice"=>27200])->value;
// Log::d("find where ", $find_where_all);

# 특정키를 중심으로 중복 데이터 제거
// $unique_arg = (new \Flex\Annona\Array\ArrayHelper( $find_where_all ))->unique("rank")->value;
// Log::d("unique_arg ", $unique_arg);

# 멀티키밸 찾기와 중복 제거 findWhere -> unique
// $findwhere_unique_arg = (new \Flex\Annona\Array\ArrayHelper( $args ))->findWhere(["productName"=>'G',"lowPrice"=>27200],'OR')->value;
// Log::d("findwhere_unique_arg ", $findwhere_unique_arg);

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


# 배열 끝에 추가 후 ---> 배열 정렬 ASC
// $append_and_sorting_asc = (new \Flex\Annona\Array\ArrayHelper( $args ))->append($append_args)->sorting('lowPrice','ASC')->value;
// Log::d('append_and_sorting_asc ', $append_and_sorting_asc);

// Log::d ("=================================");

# 값이 [숫자]로 되어 있는 키의 총합을 구한다 (SUM)
// $numeric_sum = (new \Flex\Annona\Array\ArrayHelper( $args ))->sum('lowPrice');
// Log::d('numeric_sum ', '총합  ', $numeric_sum);

# 값이 [숫자]로 되어 있는 키의 평균을 구한다 (AVG)
// $numeric_avg = (new \Flex\Annona\Array\ArrayHelper( $args ))->avg('lowPrice');
// Log::d('numeric_avg ', '평균값', $numeric_avg);

# join
// $join = (new \Flex\Annona\Array\ArrayHelper( ["a" => $args,"b" => $args2] ))->join(["a"=>"lowPrice,productName","b"=>"*"], ["a"=>'muid',"b"=>'id'])->value;
// Log::d("find all 멀티 값 방법 2", $find_all);

# union
// $union = (new \Flex\Annona\Array\ArrayHelper( ["b"=>$args2,"a"=>$args,"c"=>$args3] ))
// ->union(["a"=>"muid,lowPrice,productName","b"=>"name,userid","c"=>"title"])->value;
// Log::d("union", $union);

# union findWhere
// $union_findall = (new \Flex\Annona\Array\ArrayHelper( ["b"=>$args2,"a"=>$args,"c"=>$args3] ))
// ->union(["a"=>"muid,lowPrice,productName","b"=>"name,userid","c"=>"title"])->findAll("muid",1)->sorting('lowPrice','DESC')->value;
// Log::d("union_findall", $union_findall);

#
$args = json_decode('[{"muid":"385","dvmac":"5CF2864123A7","module_id":"comkwatercj"},{"muid":"27","dvmac":"5CF2864123A7"},{"muid":"226","dvmac":"5CF28643850B"},{"muid":"27","dvmac":"5CF2864123E5","module_id":"comkwatercj"},{"muid":"25","dvmac":"5CF2864123E5"}]',true);
Log::d($args);
$find_where_index = (new \Flex\Annona\Array\ArrayHelper( $args ))->findWhereIndex(["muid"=>27,"dvmac"=>'5CF2864123A7']);
Log::d($find_where_index);
$find_where_index = (new \Flex\Annona\Array\ArrayHelper( $args ))->findWhereIndex(["muid"=>27,"dvmac"=>'5CF2864123E5']);
Log::d($find_where_index);
?>