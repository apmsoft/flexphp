<?php
namespace Flex\Components\Data\Action;

interface ReplyInterface{
    public function doReply(?array $params=[]) : ?array;
    public function doRepl(?array $params=[]) : ?array;
}
?>