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

Log::d("===============================");
# 기본언어 설정
R::init( App::$language );
Log::d('R :: language ', R::$language);

Log::d("===============================");

# res/string 기본 호출
R::strings();
Log::d(R::$strings[R::$language]);

# 언어 데이터값을 Objects 타입으로 받기
$strings = R::dic(R::$strings[R::$language]);
Log::d( $strings->app_name );
Log::d( $strings->copy_ceo );
Log::d( $strings->app_theme_color );
Log::d( $strings->copy_right );

# res/string 배열값 추가 하기
R::strings(['cus_fax' => '02-2235-2323', 'admin_email'=>'test@dddd.com']);
Log::d('[ cus_fax, admin_email ] 추가 >>>',R::$strings[R::$language]);

Log::d("===============================");

# 기본언어 영어로 바꾸기 /==================
R::init( 'en' );
Log::d('R :: language >> ', R::$language);

# res/config : 영어 파일이 있음
R::parser(_ROOT_PATH_.'/'._CONFIG_.'/imageviewer.json', 'imageviewer2');
Log::d ( '영어파일 있음 : ',R::$r->imageviewer2 );

# [ 기본출력 ] 영어 파일이 없을 경우 기본 파일이 출력이 됨
R::parser(_ROOT_PATH_.'/'._CONFIG_.'/attachments.json', 'attachments');
Log::d ( '영어파일 없음 : ',R::$r->attachments );
# //----------------------------------

#============================
# 기본 리소스 파일 호출 : parser
#-----------------------
R::init( 'ko' );
# res/CONFIG 
#R::parser(_ROOT_PATH_.'/'._CONFIG_.'/imageviewer.json', 'imageviewer');

# res/VALUES
#R::parser(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# res/RAW
#R::parser(_ROOT_PATH_.'/'._RAW_.'/holiday.json', 'holiday');
#Log::d(R::$r->holiday);

# res/QUERY
#R::parser(_ROOT_PATH_.'/'._QUERY_.'/tables.json', 'tables');

#==================================================
# 이미 선언된 Defined 리소스 ID [ res/VALUES ]
# parserRDefinedI 로 바로 불러들일 수 있는 리소스 명
# array, sysmsg, strings, integers, floats, doubles, tables
#--------------------------------------------------
# array
R::array();
Log::d('array',R::$array);

# tables
R::tables();
Log::d('tables',R::$tables);

# integers
R::integers();
Log::d('integers',R::$integers);

# floats | doubles
R::floats();
Log::d('floats',R::$floats);

# doubles
// R::doubles();
// Log::d('doubles',R::$doubles);

# strings
R::strings();
Log::d('strings',R::$strings);

# sysmsg
R::sysmsg();
Log::d('sysmsg',R::$sysmsg);

# 예외 상황 체크 : 이미 선언된 Defined 리소스 ID 외의 것을 호출할 경우
R::ddd();
// Log::d('ddd',R::$ddd);

Log::d("===============================");

# tables : 배열값 추가 하기
R::tables(['a'=>'flex_a','b'=>'flex_b']);
Log::d('tables',R::$tables);

# 사전 Objects 타일으로 받기
$tables = R::Dic(R::$tables[R::$language]);
Log::d( $tables->member);
Log::d( $tables->a);
Log::d( $tables->b);

Log::d("===============================");

# 언어 데이터값을 Objects 타입으로 받기
$sysmsg = R::dic(R::$sysmsg[R::$language]);

// Log::e ( print_r($sysmsg,true) );
Log::e ( $sysmsg->w_aduuid );
Log::e ( $sysmsg->w_token_isnot_match );
Log::e ( $sysmsg->w_not_allowed_nation );
Log::e ( $sysmsg->e_float );
Log::e ( $sysmsg->e_filename_symbol );
?>
