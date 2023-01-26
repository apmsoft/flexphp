<?php
use Flex\Annona\Log;
use Flex\Annona\Text\TextUtil;
use Flex\Annona\Array\ArrayHelper;

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
		"productName": "A"
	},
	{
		"muid":1,
		"rank": 2,
		"lowPrice": 2000,
		"productName": "B"
	},
	{
		"muid":2,
		"rank": 4,
		"lowPrice": 5000,
		"productName": "D"
	},
	{
		"muid":2,
		"rank": 5,
		"lowPrice": 27200,
		"productName": "E"
	},
	{
		"muid":3,
		"rank": 7,
		"lowPrice": 34120,
		"productName": "G"
	},
	{
		"muid":3,
		"rank": 8,
		"lowPrice": 27200,
		"productName": "G"
	}
]';

# array data
$args = json_decode($data,true);

# ASC 
$arrays_asc = (new ArrayHelper( $args ))->sorting('lowPrice','ASC')->value;
foreach($arrays_asc as $asc)
{
    Log::d( 
        $asc['rank'], 
        $asc['lowPrice'] , 
        $asc['productName']
    );
}

Log::d ("=================================");

# DESC 
$arrays_desc = (new ArrayHelper( $args ))->sorting('lowPrice','DESC')->value;
foreach($arrays_desc as $desc)
{
    Log::d( $desc['rank'], $desc['lowPrice'], $desc['productName'] );
}

// Log::d ("=================================");

# 첫번째로 발견된 배열 받기
$find_first_args = (new ArrayHelper( $args ))->sorting('lowPrice','DESC')->find("productName","A")->value;
Log::d('find_first_args ', $find_first_args);

# 첫번째로 발견된 배열 index 키값 돌려받기
$find_index = (new ArrayHelper( $args ))->sorting('lowPrice','DESC')->findIndex("muid",2);
Log::d('find_index ', $find_index);

# 검색에 해당하는 전체 배열 받기 => (key, value)
$find_all = (new ArrayHelper( $args ))->findAll("productName","A", "G")->value;
Log::d('find_all',$find_all);

# 멀티 키=>밸류에 해당하는 값 배열 모두 찾기 AND
$find_where_all = (new ArrayHelper( $args ))->findWhere(["productName"=>'G',"lowPrice"=>27200])->value;
Log::d("find where AND", $find_where_all);

# 멀티 키=>밸류에 해당하는 값 배열 모두 찾기 OR
$find_where_all = (new ArrayHelper( $args ))->findWhere(["productName"=>'G',"lowPrice"=>27200], 'OR')->value;
Log::d("find where OR", $find_where_all);

# 특정키를 중심으로 중복 데이터 제거
$unique_arg = (new ArrayHelper( $args ))->unique("muid")->value;
Log::d("unique_arg ", $unique_arg);

# 멀티키밸 찾기와 중복 제거 findWhere -> unique
$findwhere_unique_arg = (new ArrayHelper( $args ))->findWhere(["productName"=>'G',"lowPrice"=>27200],'OR')->value;
Log::d("findwhere_unique_arg ", $findwhere_unique_arg);

Log::d ("=================================");

# 배열 끝에 추가
$append_args = [
	"rank"=> 10,
	"lowPrice"=> 22000,
	"productName"=> "J",
];

$append = (new ArrayHelper( $args ))->append( $append_args )->value;
Log::d('append ', $append);


# 배열 끝에 추가 후 ---> 배열 정렬 ASC
$append_and_sorting_asc = (new ArrayHelper( $args ))->append($append_args)->sorting('lowPrice','ASC')->value;
Log::d('append_and_sorting_asc ', $append_and_sorting_asc);

Log::d ("=================================");

# 값이 [숫자]로 되어 있는 키의 총합을 구한다 (SUM)
$numeric_sum = (new ArrayHelper( $args ))->sum('lowPrice');
Log::d('numeric_sum ', '총합  ', $numeric_sum);

# 값이 [숫자]로 되어 있는 키의 평균을 구한다 (AVG)
$numeric_avg = (new ArrayHelper( $args ))->avg('lowPrice');
Log::d('numeric_avg ', '평균값', $numeric_avg);

# 값이 [숫자]로 되어 있는 키의 MIN 값
$numeric_min = (new ArrayHelper( $args ))->min('lowPrice');
Log::d('numeric_sum ', 'MIN  ', $numeric_min);

# 값이 [숫자]로 되어 있는 키의 MAX 값
$numeric_max = (new ArrayHelper( $args ))->max('lowPrice');
Log::d('numeric_sum ', 'MAX  ', $numeric_max);

# union
$a_args = [
	["id"=>1,"name"=>"A","signdate"=>"20121100"],
	["id"=>2,"name"=>"B","signdate"=>"20121100"]
];
$b_args = [
	["id"=>1,"email"=>"aa@gmail.com","signdate"=>"20121200"],
	["id"=>2,"email"=>"bb@gmail.com","signdate"=>"20121200"]
];
$union = (new ArrayHelper( ["a"=>$a_args,"b"=>$b_args] ))
->union(["a"=>"id,name","b"=>"email"])
->value;
Log::d("union", $union);

# union findWhere
$union_findall = (new ArrayHelper( ["b"=>$args2,"a"=>$args,"c"=>$args3] ))
->union(["a"=>"muid,lowPrice,productName","b"=>"name,userid","c"=>"title"])->findAll("muid",1)->sorting('lowPrice','DESC')->value;
Log::d("union_findall", $union_findall);

# 멀티 키=>값으로 배열 index 키 찾기
$args = json_decode('[{"muid":"385","dvmac":"5CF2864123A7","module_id":"comkwatercj"},{"muid":"27","dvmac":"5CF2864123A7"},{"muid":"226","dvmac":"5CF28643850B"},{"muid":"27","dvmac":"5CF2864123E5","module_id":"comkwatercj"},{"muid":"25","dvmac":"5CF2864123E5"}]',true);
Log::d($args);

$find_where_index = (new ArrayHelper( $args ))->findWhereIndex(["muid"=>27,"dvmac"=>'5CF2864123A7']);
Log::d($find_where_index);

$find_where_index = (new ArrayHelper( $args ))->findWhereIndex(["muid"=>27,"dvmac"=>'5CF2864123E5']);
Log::d($find_where_index);

# 데이터값 중 빈값이 있는 배열만 찾기
$test_empty = [
	["no"=>1, "enumber"=>"201","eng"=>100, "math"=>90],
	["no"=>'', "enumber"=>"202","eng"=>'', "math"=>100],
	["no"=>3, "enumber"=>"203","eng"=>70, "math"=>90],
];

$fined_empty_args = (new ArrayHelper( $test_empty ))->isnull()->value;
Log::d('fined_empty_args', $fined_empty_args);

# 빈값이 있는 배열이 몇개 ?
$fined_empty_cnt = (new ArrayHelper( $test_empty ))->isnull()->sum();
Log::d('fined_empty_cnt', $fined_empty_cnt);

# 빈값 제거 후 배열 돌려받기
$fined_dropnull_args = (new ArrayHelper( $test_empty ))->dropnull()->value;
Log::d('fined_dropnull_args', $fined_dropnull_args);

# 빈값 데이터 채우기
$fined_fillnull_args = (new ArrayHelper( $test_empty ))->fillnull(['no'=>0,'eng'=>0])->value;
Log::d('fined_fillnull_args', $fined_fillnull_args);
?>