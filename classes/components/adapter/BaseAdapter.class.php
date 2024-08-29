<?php
namespace Flex\Components\Adapter;

use Flex\Annona\Paging\Relation;
class BaseAdapter {
    public const __version = '0.1';
    public array $relation = [];

    public function relation(int $total_record, int $page, int $limit, int $block) : Relation{
        $paging = new Relation($total_record, $page);
        $this->relation = $paging->query( $limit, $block)->build()->paging();

    return $paging;
    }
}

?>