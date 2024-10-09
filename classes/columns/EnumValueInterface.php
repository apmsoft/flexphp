<?php
namespace Flex\Columns;

interface EnumValueInterface
{
    public static function byName(string $name, string $case = 'UPPER'): ?object;
    public function setValue(string $key, $value): void;
    public function getValue(string $key);
}
?>