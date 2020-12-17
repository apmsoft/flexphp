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
$form->chkNumber('m', '거리', $req->m, false);
$form->chkNumber('lat', '위치정보', str_replace(array('.','-'),array('',''),$req->lat), true);
$form->chkNumber('lon', '위치정보', str_replace(array('.','-'),array('',''),$req->lon), true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
// 거리 0.1 = 100m, 1km = 1
$model->km = (trim($req->m)) ? $req->m / 1000 : R::$integers['events_distance_m'];
$model->km = $model->km + 1;

$model->page             = ($req->page) ? $req->page : 1;
$model->page_limit       = 10;          # 한 페이지당 출력 갯수
$model->page_block_count = 5;           # 한 블록의 페이지 수(5)
$model->total_record     = 0;           # 총 레코드 수
$model->table            = R::$tables['events'];   # 테이블명
$model->where            = '';          # WHERE 조건문
$model->field            = ($req->q ) ? 'address1+title' : '';   # 검색할 퀄럼명
$model->q                = ($req->q ) ? $req->q : '';       # 검색어
$model->paging_relation  = array();     # 최종완성된 페이징 배열
$model->columns          = sprintf(
    "id,title,address1,signdate,extract_id,(6371*acos(cos(radians(%s))*cos(radians(lat))*cos(radians(lon) -radians(%s))+sin(radians(%s))*sin(radians(lat)))) as distance",
    $req->lat, $req->lon, $req->lat
);

# DbHelperWhere : setBuildWhere('퀄럼명','등호(>=, &lt;=, =)','비교값', '접속사(AND | OR)')
$dbHelperWhere = new DbHelperWhere($model->field, $model->q);
if($dbHelperWhere->where){
    $model->where = ' AND ('.$dbHelperWhere->where.')';
}

# db
$db = new DbMySqli();

# total record
$total_qry = sprintf(
    "SELECT count(*) FROM (SELECT id,(6371*acos(cos(radians(%s))*cos(radians(lat))*cos(radians(lon) -radians(%s))+sin(radians(%s))*sin(radians(lat)))) as distance FROM %s GROUP BY `id`) as e WHERE (distance<%s) %s", 
    $req->lat, 
        $req->lon, 
            $req->lat, 
                $model->table, 
                    $model->km,
                        $model->where
                );
$model->total_record = $db->get_total_query( $total_qry );

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
$qry = sprintf(
    "SELECT %s FROM `%s` HAVING (distance<%s) %s ORDER BY distance ASC LIMIT %u,%d", 
    $model->columns, 
        $model->table, 
            $model->km, 
                $model->where,
                    $paging->qLimitStart,
                        $paging->pageLimit
                    );
$rlt = $db->query( $qry );

# while
$loop = array();
while( $row=$rlt->fetch_assoc() )
{
    # loop model
    $loopModel = new UtilModel( $row );
    $loopModel->distance = round($row['distance'], 2);
    $loopModel->upfiles = array();

    # 첨부파일 사진
    $upfiles_info = $db->get_record('id,file_type,directory,sfilename', R::$tables['events_upfiles'], sprintf("`extract_id`='%s' AND `is_regi`='1'", $row['extract_id']));
    if(isset($upfiles_info['id'])){
        $loopModel->upfiles = $upfiles_info;
    }
    
    # loop에 배열값 담기
    $loop[] = $loopModel->fetch();
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