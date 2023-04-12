<?php 
namespace Flex\Components\Schema;

use Flex\Components\Schema\SchemaGenerator;
use Flex\Components\Schema\TablesEnum;
use Flex\Components\Columns\ColumnsEnum;

class Tables extends SchemaGenerator
{
    public function __construct(){}

    # 관리자(매니저)
    public function manager() : string
    {
        parent::__construct(TablesEnum::MANAGER->value, TablesEnum::MANAGER->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::RECENTLY_CONNECT_DATE->value,
            ColumnsEnum::LOGOUT_TIME->value,
            ColumnsEnum::ALARM_READDATE->value,
            ColumnsEnum::LEVEL->value,
            ColumnsEnum::USERID->value,
            ColumnsEnum::PASSWD->value,
            ColumnsEnum::NAME->value,
            ColumnsEnum::IP->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['userid'=>ColumnsEnum::USERID->value]);
        return $this->create();
    }

    # 회원
    public function member() : string
    {
        parent::__construct(TablesEnum::MEMBER->value, TablesEnum::MEMBER->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::UP_DATE->value,
            ColumnsEnum::RECENTLY_CONNECT_DATE->value,
            ColumnsEnum::LOGOUT_TIME->value,
            ColumnsEnum::ALARM_READDATE->value,
            ColumnsEnum::USERID->value,
            ColumnsEnum::PASSWD->value,
            ColumnsEnum::LEVEL->value,
            ColumnsEnum::CELLPHONE->value,
            ColumnsEnum::NAME->value,
            ColumnsEnum::INTRODUCE->value,
            ColumnsEnum::AUTHEMAILKEY->value,
            ColumnsEnum::IS_PUSH->value,
            ColumnsEnum::POINT->value,
            ColumnsEnum::RECOMMAND->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['userid'=>ColumnsEnum::USERID->value]);
        return $this->create();
    }

    # 회원 포인트
    public function member_point() : string
    {
        parent::__construct(TablesEnum::MEMBER_POINT->value, TablesEnum::MEMBER_POINT->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::POINT->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::TITLE->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['muid'=>ColumnsEnum::MUID->value]);
        return $this->create();
    }

    # 공지사항
    public function bbs_notice() : string
    {
        parent::__construct(TablesEnum::BBS_NOTICE->value, TablesEnum::BBS_NOTICE->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::CATEGORY->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::HEADLINE->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value,
            ColumnsEnum::DESCRIPTION->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['headline'=>ColumnsEnum::HEADLINE->value]);
        return $this->create();
    }

    # 자주하는질문
    public function bbs_faq() : string
    {
        parent::__construct(TablesEnum::BBS_FAQ->value, TablesEnum::BBS_FAQ->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::CATEGORY->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::HEADLINE->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value,
            ColumnsEnum::DESCRIPTION->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['headline'=>ColumnsEnum::HEADLINE->value]);
        return $this->create();
    }

    # QnA
    public function bbs_qna() : string
    {
        parent::__construct(TablesEnum::BBS_QNA->value, TablesEnum::BBS_QNA->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::FID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::WID->value,
            ColumnsEnum::HEADLINE->value,
            ColumnsEnum::CATEGORY->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value,
            ColumnsEnum::DESCRIPTION->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'headline'=>ColumnsEnum::HEADLINE->value,
            'fid'=>ColumnsEnum::FID->value
        ]);
        return $this->create();
    }

    # 새소식
    public function alarm() : string
    {
        parent::__construct(TablesEnum::ALARM->value, TablesEnum::ALARM->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::USERID->value,
            ColumnsEnum::MESSAGE->value,
            ColumnsEnum::SIGNTIMESTAMP->value,
            ColumnsEnum::ITEMS->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'userid'=>ColumnsEnum::USERID->value,
            'signtimestamp'=>ColumnsEnum::SIGNTIMESTAMP->value
        ]);
        return $this->create();
    }

    # 팝업
    public function popup() : string
    {
        parent::__construct(TablesEnum::POPUP->value, TablesEnum::POPUP->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::START_DATE->value,
            ColumnsEnum::END_DATE->value,
            ColumnsEnum::VIEW_COUNT->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey(['start_date'=>ColumnsEnum::START_DATE->value.','.ColumnsEnum::END_DATE->value]);
        return $this->create();
    }

    # 쿠폰
    public function coupon() : string
    {
        parent::__construct(TablesEnum::COUPON->value, TablesEnum::COUPON->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::TOKEN->value,
            ColumnsEnum::START_DATE->value,
            ColumnsEnum::END_DATE->value,
            ColumnsEnum::SP->value,
            ColumnsEnum::PUBLICATIONS_NUMBER->value,
            ColumnsEnum::OWNERS_NUMBER->value,
            ColumnsEnum::USES_NUMBER->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::IS_PRINT->value,
            ColumnsEnum::SP_UNIT->value,
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'start_date'=>ColumnsEnum::START_DATE->value.','.ColumnsEnum::END_DATE->value
        ]);
        return $this->create();
    }

    # 쿠폰번호
    public function coupon_number() : string
    {
        parent::__construct(TablesEnum::COUPON_NUMBER->value, TablesEnum::COUPON_NUMBER->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::TOKEN->value,
            ColumnsEnum::NUMBER->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::REGIDATE->value,
            ColumnsEnum::SIGNDATE->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'token' => ColumnsEnum::TOKEN->value,
            'number' => ColumnsEnum::NUMBER->value,
            'muid' => ColumnsEnum::MUID->value,
            'cnmuid'=>ColumnsEnum::NUMBER->value.','.ColumnsEnum::MUID->value
        ]);
        return $this->create();
    }

    # 장바구니
    public function item_cart() : string
    {
        parent::__construct(TablesEnum::ITEM_CART->value, TablesEnum::ITEM_CART->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::CARTID->value,
            ColumnsEnum::ITEM_ID->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::TOTAL->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::ITEMS->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'cartid'=>ColumnsEnum::CARTID->value,
            'item_id'=>ColumnsEnum::ITEM_ID->value,
            'mitemid'=>ColumnsEnum::ITEM_ID->value.','.ColumnsEnum::MUID->value
        ]);
        return $this->create();
    }

    # 바로구매
    public function item_cart_buynow() : string
    {
        parent::__construct(TablesEnum::ITEM_CART_BUYNOW->value, TablesEnum::ITEM_CART_BUYNOW->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::CARTID->value,
            ColumnsEnum::ITEM_ID->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::TOTAL->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::ITEMS->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'cartid'=>ColumnsEnum::CARTID->value,
            'item_id'=>ColumnsEnum::ITEM_ID->value,
            'mitemid'=>ColumnsEnum::ITEM_ID->value.','.ColumnsEnum::MUID->value
        ]);
        return $this->create();
    }

    # 상품
    public function item() : string
    {
        parent::__construct(TablesEnum::ITEM->value, TablesEnum::ITEM->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::GID->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::SALE_STATE->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::LIST_PRICE->value,
            ColumnsEnum::PRICE->value,
            ColumnsEnum::SALE_PRICE->value,
            ColumnsEnum::POINT->value,
            ColumnsEnum::SOLD_OUT->value,
            ColumnsEnum::OPTION1->value,
            ColumnsEnum::OPTION2->value,
            ColumnsEnum::ORIGIN->value,
            ColumnsEnum::DELIVERY_FEE->value,
            ColumnsEnum::IS_AFTER_DELIVERY->value,
            ColumnsEnum::INDIVIDUAL_DELIVERY->value,
            ColumnsEnum::HASHTAGS->value,
            ColumnsEnum::DESCRIPTION->value,
            ColumnsEnum::EXTRACT_ID->value,
            ColumnsEnum::EXTRACT_DATA->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'gid'=>ColumnsEnum::GID->value,
            "sale_state"=>ColumnsEnum::SALE_STATE->value,
            "gidss"=>ColumnsEnum::GID->value.",".ColumnsEnum::SALE_STATE->value
        ]);
        return $this->create();
    }

    # 상품그룹
    public function item_group() : string
    {
        parent::__construct(TablesEnum::ITEM_GROUP->value, TablesEnum::ITEM_GROUP->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::GID->value,
            ColumnsEnum::TITLE->value,
            ColumnsEnum::REPLY_COUNT->value,
            ColumnsEnum::ITEM_COUNT->value,
            ColumnsEnum::IS_PRINT->value
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'gid'=>ColumnsEnum::GID->value,
            'is_print'=>ColumnsEnum::IS_PRINT->value,
            'gidprint'=>ColumnsEnum::GID->value.','.ColumnsEnum::IS_PRINT->value
        ]);
        return $this->create();
    }

    # 주문정보
    public function item_order() : string
    {
        parent::__construct(TablesEnum::ITEM_OEDER->value, TablesEnum::ITEM_OEDER->label());
        $this->columns(
            ColumnsEnum::ID->value,
            ColumnsEnum::ORDERCODE->value,
            ColumnsEnum::POINT->value,
            ColumnsEnum::MUID->value,
            ColumnsEnum::DELIVERY_FEE->value,
            ColumnsEnum::TOTAL->value,
            ColumnsEnum::SIGNDATE->value,
            ColumnsEnum::SIGNTIME->value,
            ColumnsEnum::ORDERER->value,
            ColumnsEnum::SHIPPING->value,
            ColumnsEnum::SALEPOINT->value,
            ColumnsEnum::PAYMENT->value,
            ColumnsEnum::PROOF->value,
            ColumnsEnum::ITEMS->value,
            ColumnsEnum::MEMO->value,
        );
        $this->primaryKey(ColumnsEnum::ID->value);
        $this->indexKey([
            'ordercode'=>ColumnsEnum::ORDERCODE->value
        ]);
        return $this->create();
    }
}
?>