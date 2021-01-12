<?php 
use Fus3\R\R;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

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
$form->chkPasswd('re_passwd', '비밀번호 확인', $req->re_passwd, true);
$form->chkEquals('re_passwd', '비밀번호',array($req->passwd, $req->re_passwd), true);
$form->chkName('name', '이름', $req->name, true);
$form->chkPhone('cellphone', '휴대폰번호', $req->cellphone, true);
$form->chkAlphabet('rule1', '서비스 이용 약관', $req->rule1, true);
$form->chkEquals('rule1', '서비스 이용 약관',array($req->rule1, 'y'), true);
$form->chkAlphabet('rule2', '개인정보 수집 및 이용', $req->rule2, true);
$form->chkEquals('rule2', '개인정보 수집 및 이용',array($req->rule2, 'y'), true);
// $form->chkAlphabet('rule3', '위치정보이용약관', $req->rule3, true);
// $form->chkEquals('rule3', '위치정보이용약관 수집 및 이용',array($req->rule3, 'y'), true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new Fus3\Util\UtilModel();

# db
$db = new Fus3\Db\DbMySqli();

# 이미 등록된 아이디 체크
$is_row = $db->get_record('id', R::$tables['member'], sprintf("`userid`='%s'", $req->userid));
if(isset($is_row['id'])){
    out_json( array('result'=>'false', 'msg_code'=>'w_duplicate_id', 'msg'=>R::$sysmsg['w_duplicate_id']) );
}

# insert
$db->autocommit(FALSE);
try{
    # insert query
    $db['signdate']   = time();
    $db['userid']     = $req->userid;
    $db['passwd']     = password($req->passwd);
    $db['level']      = 1;
    $db['cellphone']  = $req->cellphone;
    $db['name']       = $req->name;
    $db['extract_id'] = create_upload_token(R::$tables['member']);
    $db->insert( R::$tables['member'] );
}catch( Exception $e ){
    $db->rollback();
}

$db->commit();

# output
out_json( array(
    'result' => 'true',
    'msg'    => R::$sysmsg['v_insert']
));
?>