<?php

namespace SecureSpace\ValueObjects\Values;

class PercentValue extends FloatValue
{
    public static function fromFraction(
        float | int | FloatValue | IntegerValue $numerator,
        float | int | FloatValue | IntegerValue $denominator,
    ): self | NullValue
    {
        return 0.0 === (float) $denominator
            ? new NullValue()
            : new self((float) $numerator / $denominator);
    }

    public static function fromWhole(float | int | FloatValue | IntegerValue $value): self | NullValue
    {
        return new self($value / 100);
    }

    public function toString(): string
    {
        return number_format($this->value * 100, $this->precision) . '%';
    }
}
