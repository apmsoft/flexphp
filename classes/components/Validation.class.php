<?php
namespace Flex\Components;

use Flex\Annona\Request\FormValidation as V;
use Flex\Components\Columns\ColumnsEnum;

class Validation
{
    private $version = '0.5.5';
    private array $labels = [];
    public function __construct(array $labels){
        $this->labels = $labels;
    }

    private function check(array $column, mixed $value) : void
    {
        $_name = $column['value'];
        $_label = $this->labels[$_name];

        match($column['name']){
            "ID",
            "PRICE",
            "POINT",
            "LEVEL",
            "MUID",
            "TOTAL",
            "ITEM_ID"      => (new V($_name, $_label,$value))->null()->number(),
            "NAME"         => (new V($_name, $_label,$value))->null()->space()->disliking([]),
            "USERID"       => (new V($_name, $_label,$value))->null()->length(4,16)->space()->disliking(['_','-'])->alnum(),
            "PASSWD"       => (new V($_name, $_label,$value))->null()->length(4,16)->space()->liking(),
            "EMAIL"        => (new V($_name, $_label,$value))->null()->space()->email(),
            "BIRTHDAY",
            "START_DATE",
            "END_DATE"     => (new V($_name, $_label,$value))->null()->space()->datef(),
            "LINKURL"      => (new V($_name, $_label,$value))->null()->space()->url(),
            "EXTRACT_ID"   => (new V($_name, $_label,$value))->null()->disliking(['_','-']),
            "EXTRACT_DATA" => (new V($_name, $_label,$value))->null()->jsonf(),
            "PAGE"         => (new V($_name, $_label,$value))->number(),
            "Q"            => (new V($_name, $_label,$value))->disliking(['-','.']),
            "SOLD_OUT",
            "IS_PRINT",
            "IS_PUSH",
            "HEADLINE"     => (new V($_name, $_label,$value))->null()->alphabet(),
            "SALE_STATE"   => (new V($_name, $_label,$value))->alphabet(),
            default        => (new V($_name, $_label,$value))->null()
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