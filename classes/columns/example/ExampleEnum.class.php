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
    case TITLE    = 'title';
    case SIGNDATE = 'signdate';
    case FID      = 'fid';
}
?>