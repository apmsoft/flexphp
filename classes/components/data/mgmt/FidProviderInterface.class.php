<?php
namespace Flex\Components\Data\Mgmt;

interface FidProviderInterface
{
    public function getTable(): string;
    public function getFidColumnName(): string;
}
?>