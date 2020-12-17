<?php
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Auth\AuthSession;
use Fus3\Db\DbMySqli;
use Fus3\Db\DbHelperWhere;
use Fus3\Util\UtilModel;
use Fus3\Paging\PagingRelation;

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
$model->page             = ($req->page) ? $req->page : 1;
$model->page_limit       = 10;          # 한 페이지당 출력 갯수
$model->page_block_count = 5;           # 한 블록의 페이지 수(5)
$model->total_record     = 0;           # 총 레코드 수
$model->table            = R::$tables['mycoupon'];   # 테이블명
$model->where            = '';          # WHERE 조건문
$model->field            = ($req->q ) ? $req->field : '';   # 검색할 퀄럼명
$model->q                = ($req->q ) ? $req->q : '';       # 검색어
$model->paging_relation  = array();     # 최종완성된 페이징 배열
$model->columns          = 'a.id,a.title,a.start_date,a.end_date,a.number,b.coupon_number,b.muid';

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

# total record
$model->total_record = $db->get_total_record( R::$tables['mycoupon'], sprintf("`muid`='%u'", $meminfo['id']) );

# paging /===========
$paging = new PagingRelation('', $model->total_record, $model->page);
$paging->setQueryCount( $model->page_limit, $model->page_block_count );
$paging->setBuildQuery( array(
    'page'     => $model->page,
    'field'    => $model->field,
    'q'        => $model->q
));
$paging->buildPageRelation();

# query
$qry = sprintf("SELECT %s FROM `%s` a INNER JOIN `%s` b ON a.number=b.coupon_number %s ORDER BY b.id DESC LIMIT %u,%d", 
    $model->columns, 
        R::$tables['coupon'], 
            R::$tables['mycoupon'], 
                ($model->where) ? 'WHERE '.$model->where : '',
                    $paging->qLimitStart,
                        $paging->pageLimit);
$rlt = $db->query( $qry );

# while
// $article = ($model->total_record - $paging->pageLimit * ($paging->page - 1) ); // 순번
$loop = array();
while( $row=$rlt->fetch_assoc() )
{
    # loop model
    $loopModel = new UtilModel( $row );

    $loopModel->days_before_dday = _date_daysBeforeDDay($row['end_date']);     # 날짜출력형태 재정의
    // $loopModel->num = $article;                                 # loop model에 순번등록

    # loop에 배열값 담기
    $loop[] = $loopModel->fetch();

// $article--;
}

# output
out_json( array(
    'result'       => 'true',
    'total_page'   => $paging->totalPage,
    'total_record' => $model->total_record,
    'page'         => $model->page,
    'paging'       => $paging->printRelation(),
    'msg'          => $loop
));
?>