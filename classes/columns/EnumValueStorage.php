<?php
namespace Flex\Columns;

class EnumValueStorage
{
    private static array $values = [];

    public static function setValue(string $enumClass, string $key, $value): void
    {
        self::$values[$enumClass][$key] = $value;
    }

    public static function getValue(string $enumClass, string $key)
    {
        return self::$values[$enumClass][$key] ?? null;
    }

    public static function getValues(string $enumClass): array
    {
        return self::$values[$enumClass] ?? [];
    }

    public static function reset(string $enumClass): void
    {
        self::$values[$enumClass] = [];
    }
}
?>