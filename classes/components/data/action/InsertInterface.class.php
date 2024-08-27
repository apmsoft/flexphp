<?php
namespace Flex\Components\Data\Action;

interface InsertInterface{
    public function doInsert(?array $params=[]) : ?string;
}
?>