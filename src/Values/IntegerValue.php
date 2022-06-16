<?php

namespace SecureSpace\ValueObjects\Values;

use SecureSpace\ValueObjects\Traits\NumericTrait;

class IntegerValue extends AbstractValue
{
    use NumericTrait;

    public static function cast($value): int
    {
        return (int) $value;
    }

    public function supports($value): bool
    {
        return is_int($value);
    }

    public function toString(): string
    {
        return number_format($this->value);
    }
}
