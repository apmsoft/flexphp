<?php
use Flex\Annona\App;
use Flex\Annona\R;
use Flex\Annona\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


# model
$model = new \Flex\Annona\Model([]);
$model->total_record = 100;
$model->page         = 1;
$model->page_count   = 10;
$model->block_limit  = 2;

# pageing
$pagingRelation = new \Flex\Annona\Paging\Relation($model->total_record, $model->page);
$print_relation = $pagingRelation->setQueryCount( $model->page_count, $model->block_limit)->build()->paging();

Log::d( 'current page :', $pagingRelation->page );
Log::d( 'totalPage :', $pagingRelation->totalPage );
Log::d( $print_relation );
?>
