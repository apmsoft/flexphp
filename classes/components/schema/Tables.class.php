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

    # 상품그룹
    public function item_group() : string
    {
        parent::__construct(TablesEnum::ITEM->value, TablesEnum::ITEM->label());
        $this->columns('id','fid','title','reply_count','item_count','is_print');
        $this->primaryKey('id');
        $this->indexKey(['fid'=>'fid','is_print'=>'is_print','fidprint'=>'fid,is_print']);
        $schema = $this->create();
    return $schema;
    }

    # 관리자(매니저)
    public function manager() : string
    {
        parent::__construct(TablesEnum::MANAGER->value, TablesEnum::MANAGER->label());
        $this->columns('id','signdate','recently_connect_date','logout_time','alarm_readdate','level','userid','passwd','name','ip');
        $this->primaryKey('id');
        $this->indexKey(['userid'=>'userid']);
        $schema = $this->create();
    return $schema;
    }

    # 회원
    public function member() : string
    {
        parent::__construct(TablesEnum::MEMBER->value, TablesEnum::MEMBER->label());
        $this->columns('id','signdate','up_date','recently_connect_date','logout_time','alarm_readdate','userid','passwd','level','cellphone','name','introduce','authemailkey','is_push','point','recommand','extract_id','extract_data');
        $this->primaryKey('id');
        $this->indexKey(['userid'=>'userid']);
        $schema = $this->create();
    return $schema;
    }

    # 회원 포인트
    public function member_point() : string
    {
        parent::__construct(TablesEnum::MEMBER_POINT->value, TablesEnum::MEMBER_POINT->label());
        $this->columns('id','muid','point','signdate','title');
        $this->primaryKey('id');
        $this->indexKey(['muid'=>'muid']);
        $schema = $this->create();
    return $schema;
    }

    # 공지사항
    public function bbs_notice() : string
    {
        parent::__construct(TablesEnum::BBS_NOTICE->value, TablesEnum::BBS_NOTICE->label());
        $this->columns('id','signdate','category','muid','headline','title','extract_id','extract_data','description');
        $this->primaryKey('id');
        $this->indexKey(['headline'=>'headline']);
        $schema = $this->create();
    return $schema;
    }

    # 자주하는질문
    public function bbs_faq() : string
    {
        parent::__construct(TablesEnum::BBS_FAQ->value, TablesEnum::BBS_FAQ->label());
        $this->columns('id','signdate','category','muid','headline','title','extract_id','extract_data','description');
        $this->primaryKey('id');
        $this->indexKey(['headline'=>'headline']);
        $schema = $this->create();
    return $schema;
    }

    # QnA
    public function bbs_qna() : string
    {
        parent::__construct(TablesEnum::BBS_FAQ->value, TablesEnum::BBS_FAQ->label());
        $this->columns('id','fid','signdate','muid','wid','headline','category','title','extract_id','extract_data','description');
        $this->primaryKey('id');
        $this->indexKey(['headline'=>'headline','fid'=>'fid']);
        $schema = $this->create();
    return $schema;
    }

    # 새소식
    public function alarm() : string
    {
        parent::__construct(TablesEnum::BBS_FAQ->value, TablesEnum::BBS_FAQ->label());
        $this->columns('id','userid','message','signtimestamp','items');
        $this->primaryKey('id');
        $this->indexKey(['userid'=>'userid','signtimestamp'=>'signtimestamp']);
        $schema = $this->create();
    return $schema;
    }
}
?>