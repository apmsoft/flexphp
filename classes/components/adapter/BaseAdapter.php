<?php
namespace Flex\Components\Adapter;
use Flex\Components\Adapter\BaseAdapterInterface;

class BaseAdapter implements BaseAdapterInterface{
    public const __version = '0.1';

    public function getVersion(): string
    {
        return static::__version;
    }
}

?>