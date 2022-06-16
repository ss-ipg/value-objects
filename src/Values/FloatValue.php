<?php

namespace SecureSpace\ValueObjects\Values;

use SecureSpace\ValueObjects\Traits\NumericTrait;

class FloatValue extends AbstractValue
{
    use NumericTrait;

    public int $precision = 2;

    public static function cast($value): float
    {
        return (float) $value;
    }

    public function setPrecision(int $precision): self
    {
        $this->precision = $precision;
        $this->reformatValue();

        return $this;
    }

    public function supports($value): bool
    {
        return is_float($value);
    }

    public function toString(): string
    {
        return number_format($this->value, $this->precision);
    }
}
