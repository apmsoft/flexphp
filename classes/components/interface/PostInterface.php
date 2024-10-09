<?php
namespace Flex\Components\Interface;

interface PostInterface{
    public function doPost(?array $params=[]) : ?string;
}
?>