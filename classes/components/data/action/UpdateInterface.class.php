<?php
namespace Flex\Components\Data\Action;

interface UpdateInterface{
    public function doUpdate(?array $params=[]) : ?string;
}
?>