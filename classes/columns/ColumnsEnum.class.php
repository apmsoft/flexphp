<?php
namespace Flex\Columns;

use Flex\Columns\EntryArrayTrait;

enum ColumnsEnum : string
{
    use EntryArrayTrait;

    case ID     = 'id';
    case NAME   = 'name';
    case USERID = 'userid';
    case MUID   = 'muid';
    case TOTAL  = 'total';
    case SIGNDATE  = 'signdate';
}
?>