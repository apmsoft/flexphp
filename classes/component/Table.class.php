<?php 
namespace Flex\Component;

use Flex\Component\ColumnsEnum;

class Table {
    public function __construct(
        private string $name,
        private string $comment
    ){
    }

    public function createSchema (...$params) : string 
    {
        $schema = sprintf("CREATE TABLE `%s` (",$this->name);
        // 스키마퀄럼생성
        $schema.= sprintf(") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='%s'", $this->comment);

    return $schema;
    }
}
?>