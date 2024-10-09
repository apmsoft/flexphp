<?php
namespace Flex\Components\Interface;

interface ReplyInterface{
    public function doReply(?array $params=[]) : ?string;
}
?>