<?php
namespace Flex\ColumnsTypes;

class ValueTypes
{
    protected array $values = [];

    protected function getter($key, $chain = false, $self = null, $callback = null)
    {
        if ($chain) {
            return $self ?? $this;
        }
        $value = $this->values[$key] ?? null;
        if ($callback) {
            return $callback($value);
        }
        return $value;
    }

    protected function setter($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }

    public function values(): array
    {
        return $this->values;
    }
}