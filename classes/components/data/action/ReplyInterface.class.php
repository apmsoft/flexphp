<?php
namespace Flex\Components\Data\Action;

interface ReplyInterface{
    public function doReply(?array $params=[]) : ?string;
}
?>