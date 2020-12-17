<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 1.1
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 변수
$req = new Req;
$req->usePOST();

# form check
$form = new ReqForm();
$form->chkNull('auth_token', '토큰', $req->auth_token, true);
// $form->chkNumber('id', '회원번호', $req->id, false);
// $form->chkPhone('cell', '전화번호', $req->cell, false);

# token 비교
if(strcmp($req->auth_token, _AUTHTOKEN_)){
	out_json(array('result'=>'false','msg'=>R::$strings['e_auth_token']));
}

# db
// $db = new DbMySqli();

// #resource
// R::parserResourceDefinedID('tables');

// # 이미 있는지 확인
// $is_user = array();
// $is_user = $db->get_record('id,userid,cell',R::$tables['member'], sprintf("`id`='%s'", $req->id));
// if(!$is_user['id']){
// 	out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
// }

# 회원등급 체크
// if(isset($is_user['level']) && $is_user['level']<1){
// 	out_json(array('result'=>'false','msg_code'=>'w_not_have_permission','msg'=>'관리자에게 문의하세요'));
// }

// # 휴대폰 번호가 같은지 체크
// // if(!strcmp($is_user['cell'], $req->cell))
// // {
// 	// # UUID 비교 체크 
// 	// if(isset($is_user['uuid']) && strcmp($is_user['uuid'],$req->uuid)){
// 	// 	out_json(array('result'=>'false','msg'=>'인증받은 단말기가 아닙니다.'));
// 	// }
// // }

# 정보 업데이트
// $db['recently_connect_date']= time();
// $db->update(R::$tables['member'], sprintf("`id`='%s'", $req->id));


# output
out_json(array(
	'result'      =>'true',
	'app_version' =>R::$integers['app_version'],
	'msg'         =>''
));
?>
