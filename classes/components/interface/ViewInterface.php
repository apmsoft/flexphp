<?php
namespace Flex\Components\Interface;

interface ViewInterface{
    public function doView(?array $params=[]) : ?string;
}
?>