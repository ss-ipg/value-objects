<?php

namespace SecureSpace\ValueObjects;

class NumberValue extends AbstractValue
{
    public int $precision = 0;

    public function eq(NumberValue $n): bool
    {
        return $this->value === $n->value;
    }

    public function gt(NumberValue $n): bool
    {
        return $this->value > $n->value;
    }

    public function gte(NumberValue $n): bool
    {
        return $this->value >= $n->value;
    }

    public function isDivisibleBy(NumberValue $n): bool
    {
        return ! ($n->value === 0) && $this->value % $n->value === 0;
    }

    public function isEqual(NumberValue $n): bool
    {
        return $this->eq($n);
    }

    public function isEven(): bool
    {
        return $this->value % 2 === 0;
    }

    public function isGreaterThan(NumberValue $n): bool
    {
        return $this->gt($n);
    }

    public function isGreaterThanOrEqual(NumberValue $n): bool
    {
        return $this->gte($n);
    }

    public function isLessThan(NumberValue $n): bool
    {
        return $this->lt($n);
    }

    public function isLessThanOrEqual(NumberValue $n): bool
    {
        return $this->lte($n);
    }

    public function isNegative(): bool
    {
        return ! (null === $this->value) && $this->value < 0;
    }

    public function isOdd(): bool
    {
        return ! $this->isEven();
    }

    public function isPositive(): bool
    {
        return ! (null === $this->value) && $this->value >= 0;
    }

    public function isWhole(): bool
    {
        return is_int($this->value);
    }

    public function lt(NumberValue $n): bool
    {
        return $this->value < $n->value;
    }

    public function lte(NumberValue $n): bool
    {
        return $this->value <= $n->value;
    }

    public function setPrecision(int $precision): self
    {
        $this->precision = $precision;
        $this->reformatValue();

        return $this;
    }

    public function supports($value): bool
    {
        return is_float($value) || is_int($value);
    }

    public function toString(): string
    {
        return number_format($this->value, $this->precision);
    }
}
