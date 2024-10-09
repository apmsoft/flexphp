<?php
namespace Flex\Components\DataProcessing;

use Flex\Annona\Image\ImageGDS;

interface ImageCompressorInterface
{
    public function getImageGDS(): ImageGDS;
}
?>