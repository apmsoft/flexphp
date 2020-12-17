<?php
# use
use Fus3\Out\Out;
use Fus3\Preference\PreferenceInternalStorage;
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
use Fus3\Util\UtilSchemaMng;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
require_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
require_once '.'.DIRECTORY_SEPARATOR.'install_header.php';

echo headerHTML('install 2');

// $out_point = 'install 2 > ';
// out_print_ln = function($msg) use ($out_point) {
//     Out::prints_ln($out_point.' '.$msg.' ...'.str_pad('',4096)."\n");
//     flush();
// };
//
function out_print_ln($msg){
    Out::prints_ln('install 2 > '.$msg.' ...'.str_pad('',4096)."\n");
    flush();
}

# 디비 접속 테스트
$db=new DbMySqli();

# 테이블 목록 가져오기
out_print_ln('테이블 정보 확인하는 중');
$tables = array();
if($rlt = $db->query(sprintf("SHOW TABLES FROM `%s`", _DB_NAME_))){
    while($row = $rlt->fetch_row()){
        $tables[] = $row[0];
    }
}else{
   Out::prints_ln('테이블 목록을 가져오는데 실패 하였습니다!');
}

# 기본 셋팅에 필요한 테이블 생성.
out_print_ln('기본 셋팅에 필요한 테이블 스키마를 추출하는 중입니다..');

# 테이블 명 및 스키마 추출
$schemaMng = new UtilSchemaMng(_ROOT_PATH_.'/install/schema.sql');
$table_schema_arg = $schemaMng->fgetsTable();
#Out::prints_r($table_schema_arg);
$table_schema_count = count($table_schema_arg);

# 테이블 존재 여부 체크 및 생성
out_print_ln('테이블 중복 체크 및 스키마 생성을 시작합니다');
if(is_array($table_schema_arg))
{
    $i=1;
    foreach($table_schema_arg as $schema_info){
        $schema_table_name = $schema_info['table'];
        $schema_table_query= $schema_info['qry'];

        # 이미 존재하는지 테이블 체크
        out_print_ln($schema_table_name.' ...('.$i.'/'.$table_schema_count.')');

        if(!in_array($schema_table_name, $tables)){
            out_print_ln('-> 성공');
            $db->query($schema_table_query);
        }else{
            out_print_ln('-> 이미 존재하는 테이블');
        }
    $i++;
    }
}

# 페이지 이동
window_location('./install_finish.html');
?>
