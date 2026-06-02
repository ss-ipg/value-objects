<?php

namespace SSIPG\ValueObjects\Values;

use SSIPG\ValueObjects\Contracts\ValueInterface;

class PercentValue extends FloatValue
{
    /**
     * @phpstan-param float|int|ValueInterface<int|float> $numerator
     * @phpstan-param float|int|ValueInterface<int|float> $denominator
     *
     * @phpstan-return ($denominator is 0|0.0 ? null : self)
     */
    public static function fromFraction(
        float|int|ValueInterface $numerator,
        float|int|ValueInterface $denominator,
    ): ?self {
        $num = self::toFloat($numerator);
        $denom = self::toFloat($denominator);

        return $denom === 0.0
            ? null
            : new self($num / $denom);
    }

    /** @phpstan-param float|int|ValueInterface<int|float> $value */
    public static function fromWhole(float|int|ValueInterface $value): self
    {
        return new self(self::toFloat($value) / 100);
    }

    public function toString(): string
    {
        return $this->formatter
            ? $this->formatted
            : number_format($this->value * 100, $this->precision).'%';
    }

    /** @phpstan-param float|int|ValueInterface<int|float> $value */
    private static function toFloat(float|int|ValueInterface $value): float
    {
        return $value instanceof ValueInterface
            ? (float) $value->getValue()
            : (float) $value;
    }
}
