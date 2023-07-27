<?php
namespace Flex\Components\Data\Processing;

use Flex\Annona\Html\XssChars;
use Flex\Annona\Text\TextUtil;

final class Description extends XssChars
{
    private $version = '0.5.5';
    public function __construct(
        private string $description
    ){
        parent::__construct($description);
    }

    # urldecode
    public function urldecode() : string {
        return urldecode($this->description);
    }

    # urlencode
    public function urlencode() : string {
        return urlencode($this->description);
    }

    # 내용자르기
    public function cut(int $length) : string
    {
        return (new TextUtil( $this->description ))->cut($length)->value;
    }
}
?>