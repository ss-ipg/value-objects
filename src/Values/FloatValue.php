<?php

namespace SSIPG\ValueObjects\Values;

use SSIPG\ValueObjects\Contracts\Equatable;
use SSIPG\ValueObjects\Traits\NumericTrait;

/** @extends AbstractValue<float> */
class FloatValue extends AbstractValue implements Equatable
{
    use NumericTrait;

    public int $precision = 2;

    public static function cast(int|float|string|bool|null $value): float
    {
        return (float) $value;
    }

    public function getComparisonPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): static
    {
        $this->precision = $precision;
        $this->reformatValue();

        return $this;
    }

    public function setValue(mixed $value): static
    {
        parent::setValue($value);

        $this->value = (float) $this->value;

        return $this;
    }

    public function supports(mixed $value): bool
    {
        return is_float($value)
            || is_int($value)
            || (is_string($value) && is_numeric($value));
    }

    /** @return array{value: float, formatted: string, precision: int} */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'precision' => $this->precision,
        ]);
    }

    public function toString(): string
    {
        return number_format($this->value, $this->precision);
    }
}
