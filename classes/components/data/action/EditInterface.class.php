<?php
namespace Flex\Components\Data\Action;

interface EditInterface{
    public function doEdit(?array $params=[]) : ?array;
    public function doUpdate(?array $params=[]) : ?array;
}
?>