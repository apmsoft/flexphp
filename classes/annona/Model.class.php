<?php
namespace Flex\Annona;

class Model implements \ArrayAccess {
    public const __version = '2.0';
    private $args = [];

    public function __construct(?array $args = []) {
        if (is_array($args) && count($args)) {
            $this->args = $args;
        }
    }

    public function fetch(): array {
        return $this->args;
    }

    public function &__get(string $propertyName) {
        if (!array_key_exists($propertyName, $this->args)) {
            $this->args[$propertyName] = [];
        }
        return $this->args[$propertyName];
    }

    public function __set(string $propertyName, $value) {
        $this->args[$propertyName] = $value;
    }

    public function __isset(string $name): bool {
        return isset($this->args[$name]);
    }

    public function __unset(string $name): void {
        unset($this->args[$name]);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if (is_null($offset)) {
            $this->args[] = $value;
        } else {
            $this->args[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool {
        return isset($this->args[$offset]);
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->args[$offset]);
    }

    public function &offsetGet(mixed $offset): mixed {
        if (!isset($this->args[$offset])) {
            $this->args[$offset] = [];
        }
        return $this->args[$offset];
    }

    public function __destruct() {
        unset($this->args);
    }
}
?>