<?php

namespace SSIPG\ValueObjects\Values;

use SSIPG\ValueObjects\Contracts\Equatable;
use SSIPG\ValueObjects\Traits\NumericTrait;

/** @extends AbstractValue<int> */
class IntegerValue extends AbstractValue implements Equatable
{
    use NumericTrait;

    public static function cast(int|float|string|bool|null $value): int
    {
        return (int) $value;
    }

    public function isDivisibleBy(self $n): bool
    {
        return $n->getValue() !== 0 && $this->getValue() % $n->getValue() === 0;
    }

    public function isEven(): bool
    {
        return $this->getValue() % 2 === 0;
    }

    public function isOdd(): bool
    {
        return ! $this->isEven();
    }

    public function setValue(mixed $value): static
    {
        parent::setValue($value);

        // Two-step cast handles scientific-notation strings ("1.5e2" → 150, not 1).
        $this->value = (int) (float) $this->value;

        return $this;
    }

    public function supports(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (is_float($value)) {
            return is_finite($value) && floor($value) === $value;
        }

        if (is_string($value) && is_numeric($value)) {
            $asFloat = (float) $value;

            return is_finite($asFloat) && floor($asFloat) === $asFloat;
        }

        return false;
    }

    public function toString(): string
    {
        return number_format($this->value);
    }
}
