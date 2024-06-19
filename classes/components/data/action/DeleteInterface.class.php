<?php
namespace Flex\Components\Data\Action;

interface DeleteInterface{
    public function doDelete(?array $params=[]) : ?string;
}
?>