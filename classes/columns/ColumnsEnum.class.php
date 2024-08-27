<?php
namespace Flex\Columns;

use Flex\Columns\EntryArrayTrait;

enum ColumnsEnum : string
{
    use EntryArrayTrait;

    case ID            = 'id';
    case NAME          = 'name';
    case USERID        = 'userid';
    case muid          = 'muid';
    case Total         = 'total';

    public function type(): string
    {
        return match($this) {
            self::ID, self::NAME, self::USERID => 'string',
            self::muid, self::Total => 'int',
        };
    }
}
?>