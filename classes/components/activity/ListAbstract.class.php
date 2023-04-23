<?php 
namespace Flex\Components\Activity;

use Flex\Annona\Paging\Relation;

Abstract class ListAbstract extends Activity
{    
    public function __construct(){
        parent::add('Flex\Annona\Model',[]);
        parent::add('Flex\Annona\Array\ArrayHelper',[]);
    }

    public function page (int $page = 1) : void 
    {
        $this->model->page = $page;
    }

    public function q (string $q = '') : void {
        $this->model->q = $q ? urldecode($q) : '';
    }

    public function page_count ( int $page_count = 10) : void {
        $this->model->page_count = $page_count;
    }

    public function block_limit ( int $block_limit = 5) : void {
        $this->model->block_limit = $block_limit;
    }

    public function total_record ( int $total_record = 0) : void {
        $this->model->total_record = $total_record;
    }

    public function __set(string $k, mixed $value) : void {
        $this->model->{$k} = $value;
    }

    #@ Abstract
    public function doList(array $params) : array {}

    # paging relation
    public function pagingRelation () : void 
    {
        parent::add(Relation::class,$this->model->total_record , $this->model->page);
        $this->model->paging_relation = parent::getInstance('Relation')->query( $this->model->page_count, $this->model->block_limit )->build()->paging();
        $this->model->total_page = $paging->totalPage;
    }
}
?>