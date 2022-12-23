<?php
use Flex\Annona\App\App;
use Flex\Annona\R\R;
use Flex\Annona\Log\Log;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본값 MESSAGE_FILE, log.txt;
Log::init();
Log::init(Log::MESSAGE_ECHO);


# model
$model = new \Flex\Annona\Model\Model([]);
$model->total_record = 100;
$model->page         = 1;
$model->page_count   = 10;
$model->block_limit  = 2;

# pageing
$pagingRelation = new \Flex\Annona\Paging\PagingRelation($model->total_record, $model->page);
$pagingRelation->setQueryCount( $model->page_count, $model->block_limit);
$pagingRelation->buildPageRelation();
$print_relation = $pagingRelation->printRelation();

Log::d( 'current page :', $pagingRelation->page );
Log::d( 'totalPage :', $pagingRelation->totalPage );
Log::d( $print_relation );
?>
