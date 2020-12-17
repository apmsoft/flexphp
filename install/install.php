<?php
# use
use Fus3\Out\Out;
use Fus3\Preference\PreferenceInternalStorage;
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
require_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
require_once '.'.DIRECTORY_SEPARATOR.'install_header.php';

echo headerHTML('install 1');

// phpinfo();
// $out_point = 'install 1 > ';
// $out_print_ln = function($msg) use ($out_point) {
//     Out::prints_ln($out_point.' '.$msg.' ...'.str_pad('',4096)."\n");
//     flush();
// };

function out_print_ln($msg){
    Out::prints_ln('install 1 >  '.$msg.' ...'.str_pad('',4096)."\n");
    flush();
}

# DB 정보 체크
out_print_ln('데이터베이스 정보확인 하는중');
if(!_DB_USER_){ Out::prints_ln('DB 접속 아이디를 입력하세요'); }
if(!_DB_PASSWD_){ Out::prints_ln('DB 접속 비밀번호를 입력하세요'); }
if(!_DB_NAME_){ Out::prints_ln('DB 명을 입력하세요'); }

# DB 접속 테스트
$db = new DbMySqli();

# DB 인코딩
$db->set_encryption_mode();

# 데이터 생성 가능한 폴더가 존재하는지 체크
if(!_DATA_){ out_print_ln('파일 및 디렉토리 생성 가능한 폴더(_data)를 [707]로 생성하세요.'); }

# 파일 생성 가능한지 체크
out_print_ln('설치 가능여부 체크');
try{
    $success_msg = '설치 가능여부 체크.';
    $preferenceObj = new PreferenceInternalStorage(_ROOT_PATH_.'/'._DATA_.'/success.txt','w');
    $preferenceObj->writeInternalStorage(strval($success_msg));
}catch(Exception $e){
    out_print_ln($e->getMessage());
}

# 페이지 이동
window_location('./install_table.php','1단계 완료... 다음 2단계 테이블 생성... 페이지로 이동합니다.');
?>
