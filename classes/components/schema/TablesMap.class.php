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

    # 모델1
    public function _basic1() : TablesMap {
        $this->model->{"map+"} = parent::member();
        $this->model->{"map+"} = parent::member_point();
        $this->model->{"map+"} = parent::alarm();
        $this->model->{"map+"} = parent::manager();
        $this->model->{"map+"} = parent::bbs_notice();
        $this->model->{"map+"} = parent::bbs_faq();
        $this->model->{"map+"} = parent::popup();
    return $this;
    }

    # 쇼핑몰
    public function _item() : TablesMap
    {
        $this->model->{"map+"} = parent::item_cart();
        $this->model->{"map+"} = parent::item_cart_buynow();
        $this->model->{"map+"} = parent::item();
        $this->model->{"map+"} = parent::item_group();
        $this->model->{"map+"} = parent::item_order();
        self::_coupon();
    return $this;
    }

    # 쿠폰
    public function _coupon() : TablesMap
    {
        $this->model->{"map+"} = parent::coupon();
        $this->model->{"map+"} = parent::coupon_number();
    return $this;
    }

    public function fetchAll() : array 
    {
        return $this->model->map;
    }
}
?>