<?php
namespace Flex\Components\Action;

interface ViewInterface{
    public function doView(?array $params=[]) : array;
}
?>