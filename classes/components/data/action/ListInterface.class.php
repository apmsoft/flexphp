<?php
namespace Flex\Components\Data\Action;

interface ListInterface{
    public function doList(?array $params=[]) : ?array;
}
?>