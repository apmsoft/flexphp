<?php
namespace Flex\Components\Interface;

interface ListInterface{
    public function doList(?array $params=[]) : ?string;
}
?>