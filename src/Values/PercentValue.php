<?php

namespace SecureSpace\ValueObjects\Values;

class PercentValue extends FloatValue
{
    public static function fromFraction(
        float | int | FloatValue | IntegerValue $numerator,
        float | int | FloatValue | IntegerValue $denominator,
    ): self | NullValue
    {
        return 0 === $denominator
            ? new NullValue(null)
            : new self((float) $numerator / $denominator);
    }

    public function toString(): string
    {
        return number_format($this->value * 100, $this->precision) . '%';
    }
}
