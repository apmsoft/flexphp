<?php
namespace Flex\Components\Data\Action;

interface ReplInterface{
    public function doRepl(?array $params=[]) : ?string;
}
?>