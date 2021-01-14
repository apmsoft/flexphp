<?php 
use Fus3\R\R;
#use PushSDK\Push\PushRegister;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# Push config
// $push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
// include_once $push_sdk_root.'/config/config.push.php';
// include_once $push_sdk_root.'/classes/push/PushRegister.class.php';

# 세션
$auth=new Fus3\Auth\AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if($auth->id){
    out_json(array('result'=>'false', 'msg_code'=>'w_stay_logged_in','msg'=>R::$sysmsg['w_stay_logged_in']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Fus3\Req\Req;
$req->usePOST();

# Validation Check 예제
$form = new Fus3\Req\ReqForm();
$form->chkEmail('userid', '아이디', $req->userid, true);
$form->chkPasswd('passwd', '비밀번호', $req->passwd, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new Fus3\Util\UtilModel();

# db
$db = new Fus3\Db\DbMySqli();

# 이미 등록된 아이디 체크
$userinfo = $db->get_record('id,userid,passwd,name,level,extract_id', R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
if(!isset($userinfo['id'])){
    out_json( array('result'=>'false', 'msg_code'=>'w_enter_userid_passwd', 'msg'=>R::$sysmsg['w_enter_userid_passwd']) );
}

# 등급체크
if($userinfo['level'] < 1){
	out_json( array('result'=>'false', 'msg_code'=>'w_not_have_permission', 'msg'=>R::$sysmsg['w_not_have_permission']) );
}

# 비밀번호 비교
if($userinfo['passwd'] != password($req->passwd)){
	out_json( array('result'=>'false', 'msg_code'=>'w_enter_userid_passwd', 'msg'=>R::$sysmsg['w_enter_userid_passwd']) );
}

# 세션키 생성
$auth_arg =array();
if(is_array($app['auth'])){
	foreach($app['auth'] as $mk=>$mv){
		$auth_arg[$mv] = $userinfo[$mk];
	}
}
$auth->regiAuth($auth_arg);

# 임시토큰생성
$temp_authtoken = password($req->userid.'_'.time() );

# update
$db->autocommit(FALSE);

try{
	# 정보업데이트
	$db['recently_connect_date'] = time();
	$db['authtoken']             = $temp_authtoken;
	$db->update(R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
}catch( Exception $e ){
    $db->rollback();
}

$db->commit();

# 푸시토큰 정보 업데이트
// if($req->fcm_token && $req->fcm_token !='')
// {
// 	# 푸시 OS TYPE 체크
// 	$form->chkAlphabet('os_type', 'OS',$req->os_type, true);

// 	# 푸시 토큰 등록
// 	try{
// 		# PUSH 푸시 키
// 		$push_token = $req->fcm_token;

// 		# 푸시 키 등록하기
// 		$PushRegister = new PushRegister(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
// 		$PushRegister->push($push_token);
// 		$resp = $PushRegister->send(array(
// 			'os_type' => $req->os_type,   // [필수] 안드로이드 : a, iOS : i
// 			'id'      => $req->userid     // [필수][중복불가] 회원식별 아이디 또는 번호 [String | Integer] [test@test.com | 1] 중복되지 않도록 하세요.
// 		));

// 		#@ 성공
// 		#echo json_encode(array('result'=>'true', 'msg'=>$resp['msg']));
// 	}catch(Exception $e){
// 		out_json(array('result'=>'false','msg'=>$e->getMessage() ));
// 	}
// }

# output
out_json(array(
	'result' => 'true',
	'msg' => array(
		'userid'    => $req->userid,
		'passwd'    => $req->passwd,
		'usertoken' => $temp_authtoken,
		'name'      => $userinfo['name'],
		'level'     => $userinfo['level']
	)
));
?>