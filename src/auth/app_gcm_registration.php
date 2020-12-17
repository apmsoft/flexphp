<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\R\R;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 1.0
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 변수
$req = new Req;
$req->usePOST();

# form check
$form = new ReqForm();
$form->chkNull('auth_token', 'auth token',$req->auth_token, true);
$form->chkEmail('id', '회원아이디',$req->id, true);
$form->chkNull('os_type', 'OS',$req->os_type, true);
$form->chkNull('token', 'token',$req->token, true);

# token 비교
if(strcmp($req->auth_token, _AUTHTOKEN_)){
	out_json(array('result'=>'false','msg_code'=>'e_auth_token','msg'=>R::$strings['e_auth_token']));
}

# db
$db = new DbMySqli();

#resource
R::parserResourceDefinedID('tables');

# 사용자체크
$is_user =$db->get_record('id', R::$tables['member'], sprintf("`userid`='%s'", $req->id));
if($is_user['id']){
	# is data
	if($is_user['id']){
		$db['os_type'] 	=$req->os_type;
		$db['pushtoken']=$req->token;

		# 데이터 체크
		$is_row =$db->get_record('id,pushtoken', R::$tables['pushtoken'], sprintf("`id`='%s'", $is_user['id']));
		if($is_row['id']){
			if($req->token != $is_row['pushtoken']){
				$db->update(R::$tables['pushtoken'], sprintf("`id`='%s'",$is_user['id']));
			}
		}else{
			$db['id'] =$is_user['id'];
			$db['signdate'] =time();
			$db->insert(R::$tables['pushtoken']);
		}
	}
}

# output
out_json(array('result'=>'true','msg'=>"success"));
?>
