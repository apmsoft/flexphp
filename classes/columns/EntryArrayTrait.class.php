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
        if(count(self::names()) == count(self::values())){
            return array_combine(self::names(), self::values());
        }else{
            return [];
        }
    }

    /**
     * name : 키
     * case : 대문자로변환 upper, 소문자변환 lower, 변환없음 upper,lower 외 모든 문자
     */
    public static function byName(string $name, string $case='upper') : ?object
    {
        $NAME = ($case == strtoupper('UPPER')) ? strtoupper($name) :
                (($case == strtoupper('LOWER')) ? strtolower($name) : $name);

        if (!defined("self::{$NAME}")) {
            return null; // or throw an exception
        }

        $enum = constant("self::{$NAME}");
        $result = [
            'name'  => $enum->name,
            'value' => $enum->value
        ];
    return (object)$result;
    }
}
?>