<?php
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->usePOST();

# form check
$form = new ReqForm();
$form->chkEngNumUnderline('coupon_no', '쿠폰번호',$req->coupon_no, true);
$form->chkNull('usertoken', '유저토큰',$req->usertoken, true);
$form->chkEmail('userid', '회원아이디',$req->userid, true);

# resource
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->todate = date('Ymd');

# db
$db = new DbMySqli();

# 회원정보
$meminfo = $db->get_record('id,authtoken', R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
if(!isset($meminfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_usernotfound','msg'=>R::$sysmsg['e_usernotfound']));
}

# 토큰비교
if($meminfo['authtoken'] != $req->usertoken){
    out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match','msg'=>R::$sysmsg['w_token_isnot_match']));
}

# 같은 쿠폰 번호로 등록된 쿠폰이 있는지 체크
$coupon_info = $db->get_record('id,start_date,end_date,ea', R::$tables['coupon'], sprintf("`number`='%s'", $req->coupon_no));
if(!isset($coupon_info['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 쿠폰 기간이 만료 되었는지 체크
$coupon_info_startdate = str_replace('-','',$coupon_info['start_date']);
$coupon_info_enddate = str_replace('-','',$coupon_info['end_date']);

// 시작일 체크
if($model->todate < $coupon_info_startdate){
    out_json(array('result'=>'false','msg_code'=>'w_coupon_date_term','msg'=>R::$sysmsg['w_coupon_date_term']));
}

// 종료일 체크
if($model->todate > $coupon_info_enddate){
    out_json(array('result'=>'false','msg_code'=>'w_coupon_date_term','msg'=>R::$sysmsg['w_coupon_date_term']));
}

# 중복 쿠폰이 있는지 체크
$mycoupon_info = $db->get_record('id', R::$tables['mycoupon'], 
    sprintf("`muid`='%u' AND `coupon_number`='%s'", $meminfo['id'], $req->coupon_no));
if(isset($mycoupon_info['id'])){
    out_json(array('result'=>'false','msg_code'=>'w_duplicate_coupon','msg'=>R::$sysmsg['w_duplicate_coupon']));
}

# 쿠폰등록
$db['muid']          = $meminfo['id'];
$db['coupon_number'] = $req->coupon_no;
$db['signdate']      = time();
$db->insert(R::$tables['mycoupon']);

# 알림 메세지 등록
$db['userid']   = $req->userid;
$db['msg']      = sprintf("쿠폰(%s)을 내 쿠폰함에 저장 하였습니다.", $req->coupon_no);
$db['param']    = json_encode(array('mode'=>'mycoupon','coupon_no'=>$req->coupon_no));
$db['signdate'] = time();
$db->insert(R::$tables['alarm']);

# output
out_json(array(
    'result' => 'true',
    'msg' => R::$sysmsg['v_insert']
));
?>
