<?php
namespace Flex\Components\Interface;

interface UpdateInterface{
    public function doUpdate(?array $params=[]) : ?string;
}
?>