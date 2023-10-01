<?php
class Entry<KeyType, ValueType>
{
    protected $key;
    protected $value;

    public function __construct(KeyType $key, ValueType $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    public function getKey(): KeyType
    {
        return $this->key;
    }

    public function getValue(): ValueType
    {
        return $this->value;
    }
}
?>