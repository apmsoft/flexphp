<?php
namespace Flex\Components\Action;

interface EditInterface{
    public function doEdit(?array $params=[]) : array;
    public function doUpdate(?array $params=[]) : array;
}
?>