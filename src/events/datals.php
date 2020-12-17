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

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# Validation Check 예제
$form = new ReqForm();
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

$model->table   = R::$tables['events'];   # 테이블명
$model->where   = '';          # WHERE 조건문
$model->field   = ($req->q ) ? 'address1+title' : '';   # 검색할 퀄럼명
$model->q       = ($req->q ) ? $req->q : '';       # 검색어
$model->columns = sprintf(
    "id,lat,lon,title,address1,signdate,extract_id,(6371*acos(cos(radians(%s))*cos(radians(lat))*cos(radians(lon) -radians(%s))+sin(radians(%s))*sin(radians(lat)))) as distance",
    $req->lat, $req->lon, $req->lat
);

# DbHelperWhere : setBuildWhere('퀄럼명','등호(>=, &lt;=, =)','비교값', '접속사(AND | OR)')
$dbHelperWhere = new DbHelperWhere($model->field, $model->q);
if($dbHelperWhere->where){
    $model->where = ' AND ('.$dbHelperWhere->where.')';
}

# db
$db = new DbMySqli();

# query
$qry = sprintf(
    "SELECT %s FROM `%s` HAVING (distance<%s) %s ORDER BY distance", 
    $model->columns, 
        $model->table, 
            $model->km, 
                $model->where
            );
$rlt = $db->query( $qry );

# while
$data = array(
    'positions' => array()
);

while( $row=$rlt->fetch_assoc() )
{
    # loop model
    $loopModel = new UtilModel( $row );
    $loopModel->upfiles = array();

    # 첨부파일 사진
    $upfiles_info = $db->get_record('id,file_type,directory,sfilename', R::$tables['events_upfiles'], sprintf("`extract_id`='%s' AND `is_regi`='1'", $row['extract_id']));
    if(isset($upfiles_info['id'])){
        $loopModel->upfiles = $upfiles_info;
    }

    # data
    $data['positions'][] = array(
        'id' => $row['id'],
        'title'=> $row['title'], 
        'image'=> _SITE_HOST__SITE_HOST_.$loopModel->upfiles['directory'].'/s/'.$loopModel->upfiles['sfilename'], 
        'lat' => (double)$row['lat'],
        'lng' => (double)$row['lon']
    );
}

# output
out_json( array(
    'result' => 'true',
    'msg'    => $data
));
?>