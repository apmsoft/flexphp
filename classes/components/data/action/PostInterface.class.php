<?php
namespace Flex\Components\Data\Action;

interface PostInterface{
    public function doPost(?array $params=[]) : ?string;
}
?>