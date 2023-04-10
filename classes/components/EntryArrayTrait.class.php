<?php 
namespace Flex\Components;

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

    public static function fetchByName(string $name) : array 
    {
        $NAME = strtoupper($name);
        $enum = constant("self::{$NAME}");
        $result = [
            'name'  => $enum->name,
            'value' => $enum->value,
            'label' => $enum->label()
        ];
    return $result;
    }

    public static function fetchAll() : array 
    {
        $result = [];
        foreach(self::names() as $_NAME) {
            // echo $_NAME.PHP_EOL;
            $result[] = self::fetchByName( $_NAME );
        }
    return $result;
    }
}
?>