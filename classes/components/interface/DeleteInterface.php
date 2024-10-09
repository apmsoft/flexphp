<?php
namespace Flex\Components\Interface;

interface DeleteInterface{
    public function doDelete(?array $params=[]) : ?string;
}
?>