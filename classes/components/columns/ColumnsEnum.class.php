<?php 
namespace Flex\Components\Columns;

use Flex\Components\Columns\ColumnsEnumInterface;
use Flex\Components\EntryArrayTrait;
use Flex\Annona\R;

enum ColumnsEnum : string implements ColumnsEnumInterface
{
    use EntryArrayTrait;

    case ID           = 'id';
    case NAME         = 'name';
    case USERID       = 'userid';
    case PASSWD       = 'passwd';
    case EMAIL        = 'email';
    case BIRTHDAY     = 'birthday';
    case START_DATE   = 'start_date';
    case END_DATE     = 'end_date';
    case LINKURL      = 'linkurl';
    case VIEW_COUNT   = 'view_count';
    case TITLE        = 'title';
    case EXTRACT_ID   = 'extract_id';
    case EXTRACT_DATA = 'extract_data';
    case DESCRIPTION  = 'description';
    case SIGNDATE     = 'signdate';
    case POINT        = 'point';
    case RECOMMAND    = 'recommand';
    case IS_PUSH      = 'is_push';
    case LEVEL        = 'level';
    case CELLPHONE    = 'cellphone';
    case MUID         = 'muid';

    #@ interface
    public function label() : string 
    {
        return match($this){
            static::ID => R::columns('id'),
            static::NAME => R::columns('name'),
            static::USERID => R::columns('userid'),
            static::PASSWD => R::columns('passwd'),
            static::EMAIL => R::columns('email'),
            static::BIRTHDAY => R::columns('birthday'),
            static::START_DATE => R::columns('start_date'),
            static::END_DATE => R::columns('end_date'),
            static::LINKURL => R::columns('linkurl'),
            static::VIEW_COUNT => R::columns('view_count'),
            static::TITLE => R::columns('title'),
            static::EXTRACT_ID => R::columns('extract_id'),
            static::EXTRACT_DATA => R::columns('extract_data'),
            static::DESCRIPTION => R::columns('description'),
            static::SIGNDATE => R::columns('signdate'),
            static::POINT => R::columns('point'),
            static::RECOMMAND => R::columns('recommand'),
            static::IS_PUSH => R::columns('is_push'),
            static::LEVEL => R::columns('level'),
            static::CELLPHONE => R::columns('cellphone'),
            static::MUID => R::columns('muid')
        };
    }
}
?>