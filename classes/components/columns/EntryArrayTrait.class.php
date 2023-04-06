<?php 
namespace Flex\Components\Columns;

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
            'name'         => $enum->name(),
            'label'        => $enum->label(),
            'valueType'    => $enum->valueType(),
            'valueDefault' => $enum->valueDefault(),
            'type'         => $enum->type(),
            'length'       => $enum->length(),
            'typeNull'     => $enum->typeNull()
        ];
    return $result;
    }
}
?>