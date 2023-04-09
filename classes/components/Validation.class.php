<?php 
namespace Flex\Components;

use Flex\Annona\Request\FormValidation;
use Flex\Components\Columns\ColumnsEnum;

class Validation
{
    public function __construct(){}

    private function check(array $column, mixed $value) : void
    {
        match($column['name']){
            "ID",
            "PRICE",
            "POINT",
            "LEVEL",
            "MUID",
            "TOTAL",
            "ITEM_ID" => (new FormValidation($column['value'], $column['label'],$value))->null()->number(),
            "NAME" => (new FormValidation($column['value'], $column['label'],$value))->null()->space()->disliking([]),
            "USERID" => (new FormValidation($column['value'], $column['label'],$value))->null()->length(4,16)->space()->disliking(['_','-'])->alnum(),
            "PASSWD" => (new FormValidation($column['value'], $column['label'],$value))->null()->length(4,16)->space()->liking(),
            "EMAIL" => (new FormValidation($column['value'], $column['label'],$value))->null()->space()->email(),
            "BIRTHDAY",
            "START_DATE",
            "END_DATE" => (new FormValidation($column['value'], $column['label'],$value))->null()->space()->datef(),
            "LINKURL" => (new FormValidation($column['value'], $column['label'],$value))->null()->space()->url(),
            "EXTRACT_ID" => (new FormValidation($column['value'], $column['label'],$value))->null()->disliking(['_','-']),
            "EXTRACT_DATA" => (new FormValidation($column['value'], $column['label'],$value))->null()->jsonf(),
            "SOLD_OUT",
            "IS_PRINT",
            "IS_PUSH",
            "HEADLINE" => (new FormValidation($column['value'], $column['label'],$value))->null()->alphabet(),
            default => (new FormValidation($column['value'], $column['label'],$value))->null()
        };
    }

    public function is(string $name, mixed $resbody) : Validation 
    {
        $NAME   = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        $this->check($column, $resbody, );
    return $this;
    }
}
?>