<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\R\R;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Log\Log;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://fancy-up.tistory.com
| @Editor	: Sublime Text 3 (기본설정)
| @UPDATE	: 0.5
| @TITLE 	: php 개발 가이드 (종합)
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkNumber('id', '회원번호',$req->id, true);
$form->chkNumber('level', '관리자등급',$req->level, true);
$form->chkAlphabet('allow_ipall', '모든 IP 접속허용',$req->allow_ipall, true);
$form->chkAlphabet('allow_mobile', '모바일 접속허용',$req->allow_mobile, false);
$form->chkPasswd('passwd', '새비밀번호',$req->passwd, false);
$form->chkPasswd('re_passwd', '새비밀번호 확인',$req->re_passwd, false);
if($req->passwd){
	$form->chkEquals('re_passwd', '새비밀번호 확인',array($req->re_passwd, $req->passwd), true);
}

# ip 제한허용에 대한 데이터 추가분 체크
$allow_ip_cnt = 0;
if($req->allow_ipall =='n'){
	for($i=1; $i<4; $i++){
		$chk_ipcolumn_name = 'allow_ip'.$i;
		$chk_ipcolumn_v = $req->{$chk_ipcolumn_name};
		if($chk_ipcolumn_v && $chk_ipcolumn_v !=''){
			$allow_ip_cnt++;
		}
	}

	if($allow_ip_cnt<1){
		out_json(array('result'=>'false','msg_code'=>'e_null','msg'=>'접속허용 IP주소를 하나이상 입력하세요.'));
	}
}

# resource
R::parserResourceDefinedID('tables');

# Model
// $model = new UtilModel();

# db
$db = new DbMySqli();

# 관리자 체크
$is_adm = $db->get_record('id,level,userid',R::$tables['admmem'],sprintf("`id`='%u'",$req->id));
if(!isset($is_adm['id'])){
	out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 비밀번호
if($req->passwd){
	$cipherEncrypt = new CipherEncrypt($req->passwd);
	$_passwd = $cipherEncrypt->_md5_base64();
}

# update
$db['level']        = $req->level;
$db['allow_ipall']  = $req->allow_ipall;
$db['allow_ip1']    = $req->allow_ip1;
$db['allow_ip2']    = $req->allow_ip2;
$db['allow_ip3']    = $req->allow_ip3;
$db['allow_mobile'] = ($req->allow_mobile=='y') ? 'y' : 'n';
if($req->passwd){
	$db['passwd'] = $_passwd;
}
$db->update(R::$tables['admmem'],sprintf("`id`='%u'",$req->id));

# adm log
Log::init(R::$tables['adm_log']);
Log::d( sprintf("정보변경 ADM 아이디(%s)", $is_adm['userid']) );

# output
out_json( array(
    'result' => 'true',
    'msg'    => R::$sysmsg['v_update']
));
?>
