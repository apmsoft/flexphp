<?php 
namespace Flex\Components\Data\Model;

interface PostInterface{
    public function doPost() : array;
    public function doInsert() : array;
}
?>