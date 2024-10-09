<?php
namespace Flex\Components\Interface;

interface InsertInterface{
    public function doInsert(?array $params=[]) : ?string;
}
?>