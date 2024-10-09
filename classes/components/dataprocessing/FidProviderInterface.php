<?php
namespace Flex\Components\DataProcessing;

interface FidProviderInterface
{
    public function getTable(): string;
    public function getFidColumnName(): string;
}
?>