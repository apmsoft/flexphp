<?php
# session_start();
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

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

// strings.json        - 기본(언어별 파일이 없을 경우)
// strings_ko.json     - 한국어
// strings_en.json     - 영어
// strings_jp.json     - 일본어

# 2.2.4
# array -> arrays 로 변경

Log::d("===============================");
# 기본언어 설정
R::init( App::$language );
Log::d('R :: language ', R::$language);

Log::d("===============================");

# res/string 기본 호출
R::strings();
// Log::d(R::$strings[R::$language]);
Log::d( R::strings('app_name') );

# res/string 배열값 추가 하기
R::strings(['cus_fax' => '02-2235-2323', 'admin_email'=>'test@dddd.com']);
// Log::d('[ cus_fax, admin_email ] 추가 >>>',R::$strings[R::$language]);


Log::d("===============================");

# 기본언어 영어로 바꾸기 /==================
R::init( 'en' );
Log::d('R :: language >> ', R::$language);

#============================
# 기본 리소스 파일 호출 : parser
#-----------------------
R::init( 'ko' );
# res/CONFIG
#R::parser(_ROOT_PATH_.'/'._CONFIG_.'/imageviewer.json', 'imageviewer');

# res/VALUES
#R::parser(_ROOT_PATH_.'/'._VALUES_.'/arrays.json', 'arrays');

# res/RAW
#R::parser(_ROOT_PATH_.'/'._RAW_.'/holiday.json', 'holiday');
#Log::d(R::$r->holiday);

# res/QUERY
#R::parser(_ROOT_PATH_.'/'._QUERY_.'/tables.json', 'tables');

#==================================================
# 이미 선언된 Defined 리소스 ID [ res/VALUES ]
# parserRDefinedId 로 바로 불러들일 수 있는 리소스 명
# arrays, sysmsg, strings, integers, floats, doubles, tables
#--------------------------------------------------
# arrays
R::arrays();
// Log::d('arrays',R::$arrays);

# tables
R::tables();
// Log::d('tables',R::$tables);

# integers
R::integers();
// Log::d('integers',R::$integers);

# floats | doubles
R::floats();
// Log::d('floats',R::$floats);

# doubles
// R::doubles();
// Log::d('doubles',R::$doubles);

# strings
R::strings();
// Log::d('strings',R::$strings);

# sysmsg
R::sysmsg();
// Log::d('sysmsg',R::$sysmsg);

# fetch return array
R::fetch('tables');
Log::d('fetch function',R::fetch('tables'));

# 예외 상황 체크 : 이미 선언된 Defined 리소스 ID 외의 것을 호출할 경우
R::ddd();
// Log::d('ddd',R::$ddd);

# r
// R::parser(_ROOT_PATH_.'/config/attachments.json', 'attachments');
// Log::d('fetch attachments',R::fetch('attachments'));

Log::d("===============================");

# 설정된 언어 값 바로 받기 [ko,en...]등 설정된 언어 값
Log::d('strings',R::strings('app_name'));
Log::d('tables',R::tables('member'));
Log::d('sysmsg',R::sysmsg('w_duplicate_nickname'));
// Log::d('r',R::attachments('Upload')['file_extension']);

Log::d("===============================");

# tables : 배열값 추가 하기
R::tables(['a'=>'flex_a','b'=>'flex_b']);
Log::d('tables',R::$tables);

Log::d("===============================");

# 사전 Objects 타일으로 받기 /=========
# 언어 데이터값을 Objects 타입으로 받기
$sysmsg = R::dic(R::$sysmsg[R::$language]);
Log::e ( $sysmsg->w_aduuid );
Log::e ( $sysmsg->w_token_isnot_match );

Log::d("===============================");
# 리소스별 원하는 키에 해당하는 값들을 배열로 뽑아내기 [키 => 값] 중복키 덮여씀
$r = R::select(["strings"=>"app_name", "sysmsg"=>"v_insert,v_update"]);
Log::d('select',$r);

Log::d("===============================");
Log::d('데이터 한꺼번에 등록 및 바꾸기');
R::set('tables', ["test_db" => "test_db_v2"]);
Log::d(R::fetch('tables'));
?>
