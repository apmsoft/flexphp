<?php 
namespace Flex\Components\Data\Processing;

final class ExtractData
{
    public function __construct(
        private mixed $value
    ){}

    # urldecode
    public function json_encode() : string {
        return json_encode($this->value);
    }

    # urlencode
    public function json_decode() : string {
        return json_decode($this->value);
    }
}
?>