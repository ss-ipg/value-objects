<?php

namespace SecureSpace\ValueObjects;

class PercentValue extends NumberValue
{
    public int $precision = 2;

    /** Todo: Create a `FractionValue` value object instead? For example, fromFraction(FractionValue $fraction): self */
    public static function fromFraction(
        int | NumberValue $numerator,
        int | NumberValue $denominator,
    ): self
    {
        return 0 === $denominator
            ? new self(null)
            : new self($numerator / $denominator);
    }

    public function toString(): string
    {
        return number_format($this->value * 100, $this->precision) . '%';
    }
}
