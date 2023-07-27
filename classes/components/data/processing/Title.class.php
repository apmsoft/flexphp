<?php
namespace Flex\Components\Data\Processing;

use Flex\Annona\Text\TextUtil;

final class Title extends TextUtil
{
    private $version = '0.5.5';
    public function __construct(
        private string $title
    ){
        parent::__construct($title);
    }
}
?>