<?php

namespace SecureSpace\ValueObjects\Values;

class PercentValue extends FloatValue
{
    public static function fromFraction(
        int | IntegerValue $numerator,
        int | IntegerValue $denominator,
    ): self | NullValue
    {
        return 0 === $denominator
            ? new NullValue(null)
            : new self($numerator / $denominator);
    }

    public function toString(): string
    {
        return number_format($this->value * 100, $this->precision) . '%';
    }
}
