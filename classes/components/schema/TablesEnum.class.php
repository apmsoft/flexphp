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
    case ITEM_GROUP       = 'flex_item_group';
    case MANAGER          = 'flex_manager';
    case BBS_NOTICE       = 'flex_bbs_notice';
    case BBS_FAQ          = 'flex_bbs_faq';
    case BBS_QNA          = 'flex_bbs_qna';
    case ALARM            = 'flex_alarm';
    case POPUP            = 'flex_popup';

    #@ interface
    public function label() : string 
    {
        return match($this){
            static::MEMBER => R::components('tables')['flex_member'],
            static::MEMBER_POINT => R::components('tables')['flex_member_point'],
            static::ITEM_CART => R::components('tables')['flex_item_cart'],
            static::ITEM_CART_BUYNOW => R::components('tables')['flex_item_cart_buynow'],
            static::ITEM => R::components('tables')['flex_item'],
            static::ITEM_GROUP => R::components('tables')['flex_item_group'],
            static::MANAGER => R::components('tables')['flex_manager'],
            static::BBS_NOTICE => R::components('tables')['flex_bbs_notice'],
            static::BBS_FAQ => R::components('tables')['flex_bbs_faq'],
            static::BBS_QNA => R::components('tables')['flex_bbs_qna'],
            static::ALARM => R::components('tables')['flex_alarm'],
            static::POPUP => R::components('tables')['flex_popup']
        };
    }
}
?>