<?php 
namespace Flex\Components\Data\Processing;

use Flex\Annona\Html\XssChars;

final class Description
{
    public function __construct(
        private string $description
    ){}

    # 보기모드
    public function view(string $mode) : string 
    {
        return (new XssChars( $this->description ))->getContext($mode);
    }

    # urldecode
    public function urldecode() : string {
        return urldecode($this->description);
    }

    # urlencode
    public function urlencode() : string {
        return urlencode($this->description);
    }
}
?>