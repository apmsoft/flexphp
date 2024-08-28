<?php
namespace Flex\ColumnsTypes;

use Flex\Columns\ColumnsEnum;
use Flex\ColumnsTypes\ValueTypes;
use Flex\Annona\Date\DateTimez;

class ColumnsTypes extends ValueTypes
{
    public function __construct(){
        $this->values = [];
    }

    public function setId(int $id): ColumnsTypes{
        return $this->setter(ColumnsEnum::ID(), $id);
    }

    public function getId($chain = false): int|ColumnsTypes|null {
        return $this->getter(ColumnsEnum::ID(), $chain, $this);
    }

    public function setName(string $name): ColumnsTypes{
        return $this->setter(ColumnsEnum::NAME(), $name);
    }

    public function getName($chain = false): string|ColumnsTypes|null {
        return $this->getter(ColumnsEnum::NAME(), $chain, $this);
    }

    public function setSigndate(string $value): ColumnsTypes{
        return $this->setter(ColumnsEnum::SIGNDATE(), $value);
    }

    public function getSigndate(?string $format='Y-m-d H:i:s', $chain = false): string|ColumnsTypes|null {
        $this->setter(ColumnsEnum::SIGNDATE(), (new DateTimez( $this->values[ColumnsEnum::SIGNDATE()]))->format($format));
        return $this->getter(ColumnsEnum::SIGNDATE(), $chain, $this);
    }

    public function setUserId(string $userId): ColumnsTypes{
        return $this->setter(ColumnsEnum::USERID(), $userId);
    }

    public function getUserId($chain = false): string|ColumnsTypes|null {
        return $this->getter(ColumnsEnum::USERID(), $chain, $this);
    }

    public function setTotal(?int $total=0): ColumnsTypes{ 
        return $this->setter(ColumnsEnum::TOTAL(), $total);
    }

    public function getTotal($chain = false): int|ColumnsTypes|null {
        return $this->getter(ColumnsEnum::TOTAL(), $chain, $this);
    }
}