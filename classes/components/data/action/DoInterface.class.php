<?php
namespace Flex\Components\Data\Action;

interface DoInterface{
    public function do(?array $params=[]) : ?string;
}
?>