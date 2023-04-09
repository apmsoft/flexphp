<?php 
namespace Flex\Components\Data\Processing;

use Flex\Annona\Text\TextUtil;

final class Title extends TextUtil
{
    public function __construct(
        private string $title
    ){
        parent::__construct($title);
    }
}
?>