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

    public static function byName(string $name, string $case = 'UPPER'): ?object
    {
        $NAME = ('UPPER' == strtoupper($case)) ? strtoupper($name) :
                (('LOWER' == strtoupper($case)) ? strtolower($name) : $name);

        foreach (self::cases() as $case) {
            if (strtoupper($case->name) === $NAME) {
                return (object)[
                    'name'  => $case->name,
                    'value' => $case->value
                ];
            }
        }

        return null;
    }

    public static function __callStatic(string $name, array $args = []): string
    {
        return (self::byName($name, $args[0] ?? 'UPPER'))->value;
    }
}
?>