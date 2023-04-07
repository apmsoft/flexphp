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
    public function _item() : TablesMap
    {
        $this->model->{"map+"} = parent::item_cart();
        $this->model->{"map+"} = parent::item_cart_buynow();
        $this->model->{"map+"} = parent::item();
        $this->model->{"map+"} = parent::item_group();
    return $this;
    }

    # 회원
    public function _member() : TablesMap
    {
        $this->model->{"map+"} = parent::member();
        $this->model->{"map+"} = parent::member_point();
        $this->model->{"map+"} = parent::alarm();
    return $this;
    }

    # 관리자 매니져
    public function _manager() : TablesMap
    {
        $this->model->{"map+"} = parent::manager();
    return $this;
    }

    # BBS
    public function _bbs() : TablesMap
    {
        $this->model->{"map+"} = parent::bbs_notice();
        $this->model->{"map+"} = parent::bbs_faq();
        // $this->model->{"map+"} = parent::bbs_qna();
    return $this;
    }

    public function fetchAll() : array 
    {
        return $this->model->map;
    }
}
?>