<?php
namespace Flex\Annona\Db;

interface WhereHelperInterface {
    public function __construct(string $coord = 'AND');
    public function case(string $field_name, string $condition, mixed $value, bool $is_qutawrap = true, bool $join_detection = true): self;
    public function begin(string $coord): self;
    public function end(): self;
    public function fetch(): array;
    public function __get($propertyName): string;
}
?>