<?php 
namespace Flex\Components\Schema;

use Flex\Components\EntryEnumInterface;
use Flex\Components\EntryArrayTrait;
use Flex\Annona\R;

enum TablesEnum : string implements EntryEnumInterface
{
    use EntryArrayTrait;

    case MEMBER           = 'flex_member';
    case MEMBER_POINT     = 'flex_member_point';
    case ITEM_CART        = 'flex_item_cart';
    case ITEM_CART_BUYNOW = 'flex_item_cart_buynow';
    case ITEM             = 'flex_item';
    case MANAGER          = 'flex_manager';

    #@ interface
    public function label() : string 
    {
        return match($this){
            static::MEMBER => R::components('tables')['flex_member'],
            static::MEMBER_POINT => R::components('tables')['flex_member_point'],
            static::ITEM_CART => R::components('tables')['flex_item_cart'],
            static::ITEM_CART_BUYNOW => R::components('tables')['flex_item_cart_buynow'],
            static::ITEM => R::components('tables')['flex_item'],
            static::MANAGER => R::components('tables')['flex_manager']
        };
    }
}
?>