<?php 
namespace Flex\Components\Schema;

use Flex\Components\Schema\SchemaGenerator;
use Flex\Components\Schema\TablesEnum;

class Tables extends SchemaGenerator
{
    public function __construct(){}

    # 장바구니
    public function item_cart() : string
    {
        parent::__construct(TablesEnum::ITEM_CART->value, TablesEnum::ITEM_CART->label());
        $this->columns('id','cartid','item_id','muid','total','signdate','items');
        $this->primaryKey('id');
        $this->indexKey(['cartid'=>'cartid','item_id'=>'item_id','mitemid'=>'item_id,muid']);
        $schema = $this->create();
    return $schema;
    }

    # 바로구매
    public function item_cart_buynow() : string
    {
        parent::__construct(TablesEnum::ITEM_CART_BUYNOW->value, TablesEnum::ITEM_CART_BUYNOW->label());
        $this->columns('id','cartid','item_id','muid','total','signdate','items');
        $this->primaryKey('id');
        $this->indexKey(['cartid'=>'cartid','item_id'=>'item_id','mitemid'=>'item_id,muid']);
        $schema = $this->create();
    return $schema;
    }

    # 상품
    public function item() : string
    {
        parent::__construct(TablesEnum::ITEM->value, TablesEnum::ITEM->label());
        $this->columns('id','gid','signdate','muid','title','list_price','price','sale_price','point','sold_out','option1','option2','origin','delivery_fee','delivery_fee','is_after_delivery','individual_delivery','hashtags','description','extract_id','extract_data');
        $this->primaryKey('id');
        $this->indexKey(['gid'=>'gid']);
        $schema = $this->create();
    return $schema;
    }

}
?>