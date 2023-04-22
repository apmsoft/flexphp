<?php 
namespace Flex\Components\Activity;

use Flex\Annona\Paging\Relation;

Abstract class ListActivity extends ActivityMysql
{
    public Relation $paging;
    
    public function __construct(string $Table){
        parent::__construct($Table);
    }

    #@ Abstract
    public function doList(array $params) : array {}

    # paging relation
    public function newRelation (int $total_record, int $page, int $page_count=10, int $block_limit=5) : array 
    {
        $result = [];
        $this->paging = new Relation($total_record , $page );
        $result = $this->paging->query( $page_count, $block_limit )->build()->paging();

    return $result;
    }
}
?>