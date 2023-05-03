<?php 
namespace Flex\Components;

use Flex\Annona\Request\FormValidation;
use Flex\Components\Columns\ColumnsEnum;

class Validation
{
    private array $labels = [];
    public function __construct(array $labels){
        $this->labels = $labels;
    }

    private function check(array $column, mixed $value) : void
    {
        match($column['name']){
            "ID",
            "PRICE",
            "POINT",
            "LEVEL",
            "MUID",
            "TOTAL",
            "ITEM_ID" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->number(),
            "NAME" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->space()->disliking([]),
            "USERID" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->length(4,16)->space()->disliking(['_','-'])->alnum(),
            "PASSWD" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->length(4,16)->space()->liking(),
            "EMAIL" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->space()->email(),
            "BIRTHDAY",
            "START_DATE",
            "END_DATE" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->space()->datef(),
            "LINKURL" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->space()->url(),
            "EXTRACT_ID" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->disliking(['_','-']),
            "EXTRACT_DATA" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->jsonf(),
            "PAGE" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->number(),
            "Q" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->disliking(['-','.']),
            "SOLD_OUT",
            "IS_PRINT",
            "IS_PUSH",
            "HEADLINE" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()->alphabet(),
            "SALE_STATE" => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->alphabet(),
            default => (new FormValidation($column['value'], $this->labels[$column['value']],$value))->null()
        };
    }

    public function is(string $name, mixed $resbody) : Validation 
    {
        $NAME   = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        $this->check($column, $resbody);
    return $this;
    }
}
?>