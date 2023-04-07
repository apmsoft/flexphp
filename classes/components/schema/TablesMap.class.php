<?php 
namespace Flex\Components\Schema;

use Flex\Components\Schema\TablesEnum;
use Flex\Components\Schema\Tables;
use Flex\Annona\Model;

class TablesMap extends Tables
{
    private Model $model;
    public function __construct(){
        $this->model = new Model();
        $this->model->map = [];
    }

    # 쇼핑몰
    public function itemMap() : TablesMap
    {
        $this->model->{"map+"} = parent::item_cart();
        $this->model->{"map+"} = parent::item_cart_buynow();
        $this->model->{"map+"} = parent::item();
    return $this;
    }

    public function fetchAll() : array 
    {
        return $this->model->map;
    }
}
?>