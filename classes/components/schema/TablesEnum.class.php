<?php 
namespace Flex\Components\Schema;

enum TablesEnum : string
{
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
    case COUPON           = 'flex_coupon';
    case COUPON_NUMBER    = 'flex_coupon_number';
    case ITEM_ORDER       = 'flex_item_order';
}
?>