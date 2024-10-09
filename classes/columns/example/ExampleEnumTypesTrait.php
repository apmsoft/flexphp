<?php
namespace Flex\Columns\Example;

use Flex\Annona\Date\DateTimez;

trait ExampleEnumTypesTrait
{
    public function setId(int $id): self
    {
        $this->setValue(self::byName('ID')->value, $id);
        return $this;
    }

    public function getId(): ?int
    {
        return $this->getValue(self::byName('ID')->value) ?? null;
    }

    public function setTitle(string $title): self
    {
        $this->setValue(self::byName('TITLE')->value, $title);
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->getValue(self::byName('TITLE')->value) ?? null;
    }

    public function setSigndate(string $signdate): self
    {
        $this->setValue(self::byName('SIGNDATE')->value, $signdate);
        return $this;
    }

    public function getSigndate(?string $format = 'Y-m-d H:i:s'): ?string
    {
        $value = $this->getValue(self::byName('SIGNDATE')->value);
        return $value !== null ? (new DateTimez($value))->format($format) : null;
    }

    public function setFid(string $fid): self
    {
        $this->setValue(self::byName('FID')->value, $fid);
        return $this;
    }

    public function getFid(): ?string
    {
        return $this->getValue(self::byName('FID')->value) ?? null;
    }
}
?>