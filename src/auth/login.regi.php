<?php
use Fus3\Util\UtilController;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Db\DbMySqli;

use PushSDK\Push\PushRegister;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

$push_sdk_root = $_SERVER['DOCUMENT_ROOT'].'/libs/push_sdk';
include_once $push_sdk_root.'/config/config.push.php';
include_once $push_sdk_root.'/classes/push/PushRegister.class.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->usePOST();

# form check
$form = new ReqForm();

try{
	$controller = new UtilController($req->fetch());
	$controller->on('config');
	$controller->run('www');

	# 로그인상태 체크
	if($auth->id){
		out_json(array('result'=>'false', 'msg_code'=>'w_stay_logged_in', 'msg'=>R::$sysmsg['w_stay_logged_in']));
	}
		
	# 세션키 생성
	$auth_arg =array();
	if(is_array($app['auth'])){
		foreach($app['auth'] as $mk=>$mv){
			$auth_arg[$mv] = $controller->data['info'][$mk];
		}
	}
	$auth->regiAuth($auth_arg);

	# 임시토큰생성
	$temp_authtoken = password($req->userid.'_'.time() );

	# class
	$db = new DbMySqli();

	# 정보업데이트
	$db['recently_connect_date'] = time();
	$db['authtoken'] = $temp_authtoken;
	$db->update(R::$tables['member'], sprintf("`userid`='%s'", $req->userid));

	# 푸시토큰 정보 업데이트
	if($req->fcm_token && $req->fcm_token !='')
	{
		# 푸시 OS TYPE 체크
		$form->chkAlphabet('os_type', 'OS',$req->os_type, true);

		# 푸시 토큰 등록
		try{
			# PUSH 푸시 키
			$push_token = $req->fcm_token;

			# 푸시 키 등록하기
			$PushRegister = new PushRegister(_PUSH_PROJECTKEY_, _PUSH_ID_, _PUSH_PASSWD_);
			$PushRegister->push($push_token);
			$resp = $PushRegister->send(array(
				'os_type' => $req->os_type,   // [필수] 안드로이드 : a, iOS : i
				'id'      => $req->userid     // [필수][중복불가] 회원식별 아이디 또는 번호 [String | Integer] [test@test.com | 1] 중복되지 않도록 하세요.
			));

			#@ 성공
			#echo json_encode(array('result'=>'true', 'msg'=>$resp['msg']));
		}catch(Exception $e){
			out_json(array('result'=>'false','msg'=>$e->getMessage() ));
		}
	}

	# output
	out_json(array(
		'result' => 'true',
		'msg' => array(
			'userid' => $req->userid,
			'passwd' => $req->passwd,
			'usertoken' => $temp_authtoken,
			'name' => $controller->data['info']['name'],
			'level' => $controller->data['info']['level']
		)
	));
}catch(Exception  $e){
    // print_r($e->getTrayce);
    out_json(array('result'=>'false', 'msg_code'=>$e->getFile(), 'msg'=>$e->getMessage()));
}
?>
