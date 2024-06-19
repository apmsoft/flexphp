<?php
namespace Flex\Components\Data\Action;

interface EditInterface{
    public function doEdit(?array $params=[]) : ?string;
    public function doUpdate(?array $params=[]) : ?string;
}
?>