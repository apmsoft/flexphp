<?php
namespace Flex\Columns\Example;

use Flex\Columns\EnumValueInterface;
use Flex\Columns\EntryArrayTrait;
use Flex\Columns\EnumInstanceTrait;

enum ExampleEnum: string implements EnumValueInterface
{
    use EnumInstanceTrait;
    use EntryArrayTrait;
    use ExampleTypesTrait;

    case ID       = 'id';
    case NAME     = 'name';
    case USERID   = 'userid';
    case MUID     = 'muid';
    case TOTAL    = 'total';
    case SIGNDATE = 'signdate';
}
?>