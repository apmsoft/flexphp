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
$paging = new \Flex\Annona\Paging\Relation($model->total_record, $model->page);
$relation = $paging->query( $model->page_count, $model->block_limit)->build()->paging();

Log::d( '*** currentPage :', $paging->page );
Log::d( '*** totalPage   :', $paging->totalPage );
Log::d( '***** DB Query Limit Start :', $paging->qLimitStart ,'*****');
Log::d( '***** DB Query Limit End   :', $paging->qLimitEnd, '*****' );

Log::d( 'totalRecord :', $paging->totalRecord );
Log::d( 'blockStartPage :', $paging->blockStartPage );
Log::d( 'blockEndPage :', $paging->blockEndPage );

Log::d( '* channel', $relation );
?>
