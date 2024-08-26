<?php
namespace Flex\Columns\Member;

use Flex\Columns\EntryArrayTrait;

class AlarmEnum
{
    use EntryArrayTrait;

    const ID       = 'id';
    const USERID   = 'userid';
    const MSG      = 'msg';
    const PARAM    = 'param';
    const SIGNDATE = 'signdate';
    const ISREAD   = 'isread';
}
?>