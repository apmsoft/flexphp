<?php
use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;
use Flex\Annona\Request\Request;
use Flex\Annona\Request\FormValidation;
use Flex\Annona\Model;
use Flex\Annona\Paging\Relation;
use Flex\Annona\Date\DateTimez;

# config
$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# Log
Log::init(Log::MESSAGE_FILE, sprintf("%s/log%s.out",_DATA_,date('Ymd')));
Log::setDebugs('i','d','v','e');
Log::options([
    'datetime'   => true,
    'debug_type' => true,
    'newline'    => true
]);

# request
$request = (object)(new Request())->get()->fetch();

# Form Validation
try{
    (new FormValidation('page','페이지',$request->page))->number();
    (new FormValidation('q','검색어',$request->q))->disliking(['-','.']);
}catch(\Exception $e){
    Log::e($e->getMessage());
    return json_decode($e->getMessage(),true);
}

# resource
R::tables();
R::array();
$sysmsg = R::dic(R::$sysmsg[R::$language]);
$tables = R::dic(R::$tables[R::$language]);
$array  = R::dic(R::$array[R::$language]);

# Database
$db = new DbMySqli();

# Model
$model = new Model();
$model->total_record = 0;
$model->page         = $request->page ?? 1;
$model->page_count   = 10;
$model->block_limit  = 2;
$model->data         = [];

# total record
$model->total_record = $db->table($tables->member)->total();

# pageing
$paging = new \Flex\Annona\Paging\Relation($model->total_record, $model->page);
$relation = $paging->query( $model->page_count, $model->block_limit)->build()->paging();

# query
$rlt = $db->table($tables->member)->select('id','name','userid','cellphone','signdate')->where(
    (new WhereHelper)->
        begin('OR')
            ->case('name','LIKE',$request->q)
            ->case('userid','LIKE',$request->q)
            ->case('cellphone','LIKE',$request->q)
        ->end()
    ->where
)->orderBy('id desc')->limit($paging->qLimitStart, $paging->qLimitEnd)->query();

$article = ($model->total_record - $paging->pageLimit * ($paging->page - 1) ); // 순번
while($row = $rlt->fetch_assoc())
{
    # loop model
    $loopModel = new Model( $row );

    # 순번 추가
    $loopModel->num = $article;

    # data 담기
    $model->{"data+"} = $loopModel->fetch();
$article--;
}

# output
return [
    "result"       => 'true',
    'total_page'   => $paging->totalPage,
    'total_record' => $paging->totalRecord,
    'page'         => $paging->page,
    'paging'       => $relation,
    "msg"          => $model->data
];
?>