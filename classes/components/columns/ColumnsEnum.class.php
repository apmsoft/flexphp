<?php 
namespace Flex\Components\Columns;

use Flex\Components\EntryEnumInterface;
use Flex\Components\EntryArrayTrait;
use Flex\Annona\R;

enum ColumnsEnum : string implements EntryEnumInterface
{
    use EntryArrayTrait;

    case ID                    = 'id';
    case NAME                  = 'name';
    case USERID                = 'userid';
    case PASSWD                = 'passwd';
    case RE_PASSWD             = 're_passwd';
    case EMAIL                 = 'email';
    case BIRTHDAY              = 'birthday';
    case START_DATE            = 'start_date';
    case END_DATE              = 'end_date';
    case LINKURL               = 'linkurl';
    case VIEW_COUNT            = 'view_count';
    case TITLE                 = 'title';
    case EXTRACT_ID            = 'extract_id';
    case EXTRACT_DATA          = 'extract_data';
    case DESCRIPTION           = 'description';
    case SIGNDATE              = 'signdate';
    case SIGNTIME              = 'signtime';
    case SIGNTIMESTAMP         = 'signtimestamp';
    case POINT                 = 'point';
    case RECOMMAND             = 'recommand';
    case IS_PUSH               = 'is_push';
    case LEVEL                 = 'level';
    case CELLPHONE             = 'cellphone';
    case MUID                  = 'muid';
    case TOTAL                 = 'total';
    case CARTID                = 'cartid';
    case DVMAC                 = 'dvmac';
    case UP_DATE               = 'up_date';
    case RECENTLY_CONNECT_DATE = 'recently_connect_date';
    case LOGOUT_TIME           = 'logout_time';
    case ALARM_READDATE        = 'alarm_readdate';
    case INTRODUCE             = 'introduce';
    case AUTHEMAILKEY          = 'authemailkey';
    case HEADLINE              = 'headline';
    case CATEGORY              = 'category';
    case IP                    = 'ip';
    case USAGE_INT             = 'usage_int';
    case ACCESS_TOKEN          = 'access_token';
    case TOKEN                 = 'token';
    case SECRET_KEY            = 'secret_key';
    case MODULE_ID             = 'module_id';
    case ITEM_ID               = 'item_id';
    case ITEMS                 = 'items';
    case GID                   = 'gid';
    case LIST_PRICE            = "list_price";
    case PRICE                 = "price";
    case SALE_PRICE            = "sale_price";
    case SOLD_OUT              = "sold_out";
    case OPTION1               = "option1";
    case OPTION2               = "option2";
    case ORIGIN                = "origin";
    case DELIVERY_FEE          = "delivery_fee";
    case IS_AFTER_DELIVERY     = "is_after_delivery";
    case INDIVIDUAL_DELIVERY   = "individual_delivery";
    case HASHTAGS              = "hashtags";
    case FID                   = "fid";
    case WID                   = "wid";
    case MESSAGE               = "message";
    case IS_PRINT              = 'is_print';
    case REPLY_COUNT           = "reply_count";
    case ITEM_COUNT            = 'item_count';
    case Q                     = 'q';
    case PAGE                  = 'page';
    case NEWPASSWD             = 'newpasswd';
    case RE_NEWPASSWD          = 're_newpasswd';
    case SALE_STATE            = 'sale_state';
    case REGIDATE              = 'regidate';
    case NUMBER                = 'number';
    case SP                    = 'sp';
    case PUBLICATIONS_NUMBER   = 'publications_number';
    case OWNERS_NUMBER         = 'owners_number';
    case USES_NUMBER           = 'uses_number';
    case SP_UNIT   = 'sp_unit';
    case ORDERCODE = "ordercode";
    case PAYMETHOD = "paymethod";
    case PAYMENT   = "payment";
    case TERM      = "term";
    case MEMO      = "memo";
    case ORDERER   = "orderer";
    case SHIPPING  = "shipping";
    case SALEPOINT = "salepoint";
    case PROOF     = "proof";


    #@ interface
    public function label() : string 
    {
        return match($this){
            static::ID => R::components('columns')['id'],
            static::NAME => R::components('columns')['name'],
            static::USERID => R::components('columns')['userid'],
            static::PASSWD => R::components('columns')['passwd'],
            static::RE_PASSWD => R::components('columns')['re_passwd'],
            static::EMAIL => R::components('columns')['email'],
            static::BIRTHDAY => R::components('columns')['birthday'],
            static::START_DATE => R::components('columns')['start_date'],
            static::END_DATE => R::components('columns')['end_date'],
            static::LINKURL => R::components('columns')['linkurl'],
            static::VIEW_COUNT => R::components('columns')['view_count'],
            static::TITLE => R::components('columns')['title'],
            static::EXTRACT_ID => R::components('columns')['extract_id'],
            static::EXTRACT_DATA => R::components('columns')['extract_data'],
            static::DESCRIPTION => R::components('columns')['description'],
            static::SIGNDATE => R::components('columns')['signdate'],
            static::SIGNTIMESTAMP => R::components('columns')['signtimestamp'],
            static::POINT => R::components('columns')['point'],
            static::RECOMMAND => R::components('columns')['recommand'],
            static::IS_PUSH => R::components('columns')['is_push'],
            static::LEVEL => R::components('columns')['level'],
            static::CELLPHONE => R::components('columns')['cellphone'],
            static::MUID => R::components('columns')['muid'],
            static::TOTAL => R::components('columns')['total'],
            static::CARTID => R::components('columns')['cartid'],
            static::DVMAC => R::components('columns')['dvmac'],
            static::UP_DATE => R::components('columns')['up_date'],
            static::RECENTLY_CONNECT_DATE => R::components('columns')['recently_connect_date'],
            static::LOGOUT_TIME => R::components('columns')['logout_time'],
            static::ALARM_READDATE => R::components('columns')['alarm_readdate'],
            static::INTRODUCE => R::components('columns')['introduce'],
            static::AUTHEMAILKEY => R::components('columns')['authemailkey'],
            static::HEADLINE => R::components('columns')['headline'],
            static::CATEGORY => R::components('columns')['category'],
            static::IP => R::components('columns')['ip'],
            static::USAGE_INT => R::components('columns')['usage_int'],
            static::ACCESS_TOKEN => R::components('columns')['access_token'],
            static::SECRET_KEY => R::components('columns')['secret_key'],
            static::MODULE_ID => R::components('columns')['module_id'],
            static::ITEM_ID => R::components('columns')['item_id'],
            static::ITEMS => R::components('columns')['items'],
            static::GID => R::components('columns')['gid'],
            static::LIST_PRICE => R::components('columns')['list_price'],
            static::PRICE => R::components('columns')['price'],
            static::SALE_PRICE => R::components('columns')['sale_price'],
            static::SOLD_OUT => R::components('columns')['sold_out'],
            static::OPTION1 => R::components('columns')['option1'],
            static::OPTION2 => R::components('columns')['option2'],
            static::ORIGIN => R::components('columns')['origin'],
            static::DELIVERY_FEE => R::components('columns')['delivery_fee'],
            static::IS_AFTER_DELIVERY => R::components('columns')['is_after_delivery'],
            static::INDIVIDUAL_DELIVERY => R::components('columns')['individual_delivery'],
            static::HASHTAGS => R::components('columns')['hashtags'],
            static::FID => R::components('columns')['fid'],
            static::WID => R::components('columns')['wid'],
            static::MESSAGE => R::components('columns')['message'],
            static::IS_PRINT => R::components('columns')['is_print'],
            static::REPLY_COUNT => R::components('columns')['reply_count'],
            static::ITEM_COUNT => R::components('columns')['item_count'],
            static::Q => R::components('columns')['q'],
            static::PAGE => R::components('columns')['page'],
            static::NEWPASSWD => R::components('columns')['newpasswd'],
            static::RE_NEWPASSWD => R::components('columns')['re_newpasswd'],
            static::SALE_STATE => R::components('columns')['sale_state'],
            static::REGIDATE => R::components('columns')['regidate'],
            static::TOKEN => R::components('columns')['token'],
            static::NUMBER => R::components('columns')['number'],
            static::SP => R::components('columns')['sp'],
            static::PUBLICATIONS_NUMBER => R::components('columns')['publications_number'],
            static::OWNERS_NUMBER => R::components('columns')['owners_number'],
            static::USES_NUMBER => R::components('columns')['uses_number'],
            static::SP_UNIT => R::components('columns')['sp_unit'],
            static::ORDERCODE => R::components('columns')['ordercode'],
            static::PAYMETHOD => R::components('columns')['paymethod'],
            static::PAYMENT => R::components('columns')['payment'],
            static::TERM => R::components('columns')['term'],
            static::MEMO => R::components('columns')['memo'],
            static::ORDERER => R::components('columns')['orderer'],
            static::SHIPPING => R::components('columns')['shipping'],
            static::SALEPOINT => R::components('columns')['salepoint'],
            static::PROOF => R::components('columns')['proof']
        };
    }
}
?>