<?php
namespace Flex\Components\Action;

interface PostInterface{
    public function doPost(?array $params=[]) : array;
    public function doInsert(?array $params=[]) : array;
}
?>