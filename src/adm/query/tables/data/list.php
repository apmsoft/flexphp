<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\Db\DbHelperWhere;
use Fus3\Paging\PagingRelation;

# config
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
$form->chkEngNumUnderline('tname', '테이블명', $req->tname, true);
$form->chkNumber('page', '페이지', $req->page, false);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('tables');

# check
if(!isset(R::$tables[$req->tname])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# model
$model = new UtilModel();
$model->page             = ($req->page) ? $req->page : 1;
$model->page_limit       = 10;          # 한 페이지당 출력 갯수
$model->page_block_count = 5;           # 한 블록의 페이지 수(5)
$model->total_record     = 0;           # 총 레코드 수
$model->table            = R::$tables[$req->tname];   # 테이블명
$model->where            = '';          # WHERE 조건문
$model->field            = ($req->q ) ? $req->field : '';   # 검색할 퀄럼명
$model->q                = ($req->q ) ? $req->q : '';       # 검색어
$model->paging_relation  = array();     # 최종완성된 페이징 배열
$model->columns          = '*';
$model->primary_key = '';


# DbHelperWhere : setBuildWhere('퀄럼명','등호(>=, &lt;=, =)','비교값', '접속사(AND | OR)')
$dbHelperWhere = new DbHelperWhere($model->field, $model->q);
if($dbHelperWhere->where){
    $model->where = $dbHelperWhere->where;
}

# db
$db = new DbMySqli();

$columns = array();
$column_types = array();
$rlt = $db->query(sprintf("SELECT COLUMN_NAME,COLUMN_KEY,COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $model->table));
while($row = $rlt->fetch_assoc())
{
	if($model->primary_key ==''){
		if($row['COLUMN_KEY'] == 'PRI'){
			$model->primary_key = $row['COLUMN_NAME'];
		}
    }
    $_name = $row['COLUMN_NAME'];
    $_type = $row['COLUMN_TYPE'];

	$columns[] = $_name;
    $column_types[$_name] = $_type;
}

# total record
$model->total_record = $db->get_total_record( $model->table, $model->where );

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
$qry = sprintf("SELECT %s FROM `%s` %s ORDER BY %s DESC LIMIT %u,%d", 
    $model->columns, 
        $model->table, 
            ($model->where) ? 'WHERE '.$model->where : '', 
                $model->primary_key,
                    $paging->qLimitStart,
                        $paging->pageLimit
);
$rlt = $db->query( $qry );

# while
$loop = array();
while( $row=$rlt->fetch_assoc() )
{
    foreach($row as $rk => $rv){
        if(isset($column_types[$rk]))
        {
            $_type = $column_types[$rk];
            $row[$rk] = array(
                'data' => $rv,
                'column_type' => $_type
            );

            if( strpos($_type, 'varchar') !==false){
                $row[$rk] = array(
                    'data' => str_cut($rv, 100),
                    'column_type' => 'varchar'
                );
            }else if(strpos($_type, 'text') !==false){
                $row[$rk] = array(
                    'data' => str_cut($rv, 300),
                    'column_type' => 'text'
                );
            }
        }
    }

    # loop에 배열값 담기
    $loop[] = $row;
}

# output
out_json( array(
    'result'       => 'true',
    'total_page'   => $paging->totalPage,
    'total_record' => $model->total_record,
    'page'         => $model->page,
    'paging'       => $paging->printRelation(),
    'columns'      => $columns,
    'primary_key'  => $model->primary_key,
    'q'            => $model->q,
    'field'        => $model->field,
    'msg'          => $loop
));
?>
