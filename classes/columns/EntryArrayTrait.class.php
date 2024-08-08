<?php
namespace Flex\Columns;

trait EntryArrayTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        if (count(self::names()) == count(self::values())) {
            return array_combine(self::names(), self::values());
        } else {
            return [];
        }
    }

    public static function byName(string $name, string $case = 'UPPER'): mixed
    {
        $NAME = ('UPPER' == strtoupper($case)) ? strtoupper($name) :
                (('LOWER' == strtoupper($case)) ? strtolower($name) : $name);

        if (!defined(self::class . "::{$NAME}")) {
            return null; // or throw an exception
        }

        $enum = constant(self::class . "::{$NAME}");
        $result = (object)[
            'name'  => $enum->name,
            'value' => $enum->value
        ];
        return $result;
    }

    public static function __callStatic(string $name, array $args = []): string
    {
        return (self::byName($name, $args[0] ?? 'UPPER'))->value;
    }
}
?>