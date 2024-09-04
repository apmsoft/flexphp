<?php
namespace Flex\Columns\Example;

use Flex\Annona\Date\DateTimez;

trait ExampleTypesTrait
{
    public function setId(int $id): self
    {
        ($enum = self::byName('ID')) && $this->setValue($enum->value, $id);
        return $this;
    }

    public function getId(): ?int
    {
        return ($enum = self::byName('ID')) !== null ? $this->getValue($enum->value) : null;
    }

    public function setMuid(int $muid): self
    {
        ($enum = self::byName('MUID')) && $this->setValue($enum->value, $muid);
        return $this;
    }

    public function getMuid(): ?int
    {
        return ($enum = self::byName('MUID')) !== null ? $this->getValue($enum->value) : null;
    }

    public function setName(string $name): self
    {
        ($enum = self::byName('NAME')) && $this->setValue($enum->value, $name);
        return $this;
    }

    public function getName(): ?string
    {
        return ($enum = self::byName('NAME')) !== null ? $this->getValue($enum->value) : null;
    }

    public function setUserId(string $userId): self
    {
        ($enum = self::byName('USERID')) && $this->setValue($enum->value, $userId);
        return $this;
    }

    public function getUserId(): ?string
    {
        return ($enum = self::byName('USERID')) !== null ? $this->getValue($enum->value) : null;
    }

    public function setSigndate(string $signdate): self
    {
        ($enum = self::byName('SIGNDATE')) && $this->setValue($enum->value, $signdate);
        return $this;
    }

    public function getSigndate(?string $format = 'Y-m-d H:i:s'): ?string
    {
        if (($enum = self::byName('SIGNDATE')) === null) {
            return null;
        }
        $value = $this->getValue($enum->value);
        return $value !== null ? (new DateTimez($value))->format($format) : null;
    }

    public function setTotal(int $total): self
    {
        ($enum = self::byName('TOTAL')) && $this->setValue($enum->value, $total);
        return $this;
    }

    public function getTotal(): ?int
    {
        return ($enum = self::byName('TOTAL')) !== null ? $this->getValue($enum->value) : null;
    }
}
?>