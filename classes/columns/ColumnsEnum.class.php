<?php
namespace Flex\Columns;

use Flex\Columns\EntryArrayTrait;

enum ColumnsEnum : string
{
    use EntryArrayTrait;

    case ID            = 'id';
    case NAME          = 'name';
    case USERID        = 'userid';
    case PASSWD        = 'passwd';
    case RE_PASSWD     = 're_passwd';
    case EMAIL         = 'email';
    case BIRTHDAY      = 'birthday';
    case START_DATE    = 'start_date';
    case END_DATE      = 'end_date';
    case LINKURL       = 'linkurl';
    case VIEW_COUNT    = 'view_count';
    case TITLE         = 'title';
    case EXTRACT_ID    = 'extract_id';
    case EXTRACT_DATA  = 'extract_data';
    case DESCRIPTION   = 'description';
    case SIGNDATE      = 'signdate';
    case SIGNTIME      = 'signtime';
    case SIGNTIMESTAMP = 'signtimestamp';
    case POINT         = 'point';
    case RECOMMAND     = 'recommand';
    case IS_PUSH       = 'is_push';
    case LEVEL         = 'level';
    case CELLPHONE     = 'cellphone';
    case muid          = 'muid';
    case Total         = 'total';
}
?>