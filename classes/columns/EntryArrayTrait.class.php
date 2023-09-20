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
     * upper : 대문자로변환 하기 true
     */
    public static function byName(string $name, bool $upper=true) : object
    {
        $NAME = ($upper) ? strtoupper($name) : $name;
        $enum = constant("self::{$NAME}");
        $result = [
            'name'  => $enum->name,
            'value' => $enum->value
        ];
    return (object)$result;
    }
}
?>