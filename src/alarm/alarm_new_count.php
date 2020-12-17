<?php
// use Fus3\Auth\AuthSession;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;

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
// $auth=new AuthSession($app['auth']);
// $auth->sessionStart();

// # 로그인 상태 체크
// if(!$auth->id){
//     out_json(array('result'=>'false', 'total'=>'0', 'msg'=>$auth->id));
// }

# 변수
$req = new Req;
$req->useGET();

# form check
$form = new ReqForm();
$form->chkNull('usertoken', '유저토큰',$req->usertoken, true);
$form->chkEmail('userid', '회원아이디',$req->userid, true);

# db 선언 및 접속
$db = new DbMySqli();

# resource
R::parserResourceDefinedID('tables');

# 회원정보
$meminfo = $db->get_record('id,alarm_readdate,authtoken', R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
if(!isset($meminfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_usernotfound','msg'=>R::$sysmsg['e_usernotfound']));
}

# 토큰비교
if($meminfo['authtoken'] != $req->usertoken){
    out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match','msg'=>R::$sysmsg['w_token_isnot_match']));
}

# query sample 1 /=========
$total = $db->get_total_record(R::$tables['alarm'], sprintf("`userid`='%s' AND `isread`>%s", $req->userid, $meminfo['alarm_readdate']));

# output
out_json(array(
    'result' =>'true',
    'total'  =>$total,
    'msg'    =>array()
));
?>
