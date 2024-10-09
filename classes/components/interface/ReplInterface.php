<?php
namespace Flex\Components\Interface;

interface ReplInterface{
    public function doRepl(?array $params=[]) : ?string;
}
?>