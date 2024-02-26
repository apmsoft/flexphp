<?php
namespace Flex\Components\Data\Action;

interface ViewInterface{
    public function doView(?array $params=[]) : array;
}
?>