<?php 
namespace Flex\Component;

interface ColumnsInterface {
    public function name() : string;
    public function label() : string;
    public function valueType() : string;
    public function valueDefault() : mixed;
    public function type() : string;
    public function length() : mixed;
    public function typeNull() : string;
}
?>