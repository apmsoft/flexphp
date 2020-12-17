<?php
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Db\DbMySqli;
use Fus3\Db\DbHelperWhere;
use Fus3\Util\UtilModel;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

# 세션시작
$auth = new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
#if(!$auth->id){
#	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
#}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# Validation Check 예제
$form = new ReqForm();
$form->chkNumber('page', '페이지', $req->page, false);
$form->chkNull('usertoken', '유저토큰',$req->usertoken, true);
$form->chkEmail('userid', '회원아이디',$req->userid, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->where            = '';          # WHERE 조건문
$model->field            = ($req->q ) ? $req->field : '';   # 검색할 퀄럼명
$model->q                = ($req->q ) ? $req->q : '';       # 검색어
$model->columns          = 'a.id,a.title,a.start_date,a.end_date,a.number,a.description,b.coupon_number,b.muid';

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

# DbHelperWhere : setBuildWhere('퀄럼명','등호(>=, &lt;=, =)','비교값', '접속사(AND | OR)')
$dbHelperWhere = new DbHelperWhere($model->field, $model->q);
$dbHelperWhere->setBuildWhere('b.muid','=', $meminfo['id'], 'AND');
if($dbHelperWhere->where){
    $model->where = $dbHelperWhere->where;
}

# query
$qry = sprintf("SELECT %s FROM `%s` a INNER JOIN `%s` b ON a.number=b.coupon_number %s", 
    $model->columns, 
        R::$tables['coupon'], 
            R::$tables['mycoupon'], 
                ($model->where) ? 'WHERE '.$model->where : ''
);
$rlt = $db->query( $qry );
$row = $rlt->fetch_assoc();
if(!isset($row['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# view model
$viewModel = new UtilModel( $row );
$viewModel->description = contextType($row['description'],'XSS');
$viewModel->days_before_dday = _date_daysBeforeDDay($row['end_date']);

# output
out_json( array(
    'result' => 'true',
    'msg'    => $viewModel->fetch()
));
?>