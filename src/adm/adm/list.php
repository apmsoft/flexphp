<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\R\R;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;

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
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();

# resource
R::parserResourceDefinedID('tables');

# Model
$model = new UtilModel();
$model->total_record     = 0;           # 총 레코드 수
$model->table            = R::$tables['admmem'];   # 테이블명
$model->where            = '';          # WHERE 조건문
$model->field            = ($req->q ) ? $req->field : '';   # 검색할 퀄럼명
$model->q                = ($req->q ) ? $req->q : '';       # 검색어
$model->columns          = 'id,name,userid,level,recently_connect_date';
$model->qry              = '';
$model->qry_binding      = "SELECT %s FROM `%s` %s ORDER BY `id` DESC";

# db
$db = new DbMySqli();

# total record
$model->total_record = $db->get_total_record( $model->table, $model->where );

# query
$model->qry = sprintf($model->qry_binding ,
    $model->columns,
        $model->table, 
            ($model->where) ? 'WHERE '.$model->where : ''
);
$rlt=$db->query( $model->qry );

# while
$loop = array();
while( $row=$rlt->fetch_assoc() )
{
    # loop model
    $loopModel = new UtilModel( $row );
    $loopModel->recently_connect_date = ($row['recently_connect_date']) ? timetodatetime($row['recently_connect_date']) : 0;     # 날짜출력형태 재정의

    # loop에 배열값 담기
    $loop[] = $loopModel->fetch();
}

# output
out_json( array(
    'result'       => 'true',
    'total_record' => $model->total_record,
    'msg'          => $loop
));
?>
