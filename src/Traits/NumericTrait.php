<?php

namespace SecureSpace\ValueObjects\Traits;

use SecureSpace\ValueObjects\Values\ValueInterface;

trait NumericTrait
{
    public function eq(ValueInterface $n): bool
    {
        return $this->value === $n->value;
    }

    public function gt(ValueInterface $n): bool
    {
        return $this->value > $n->value;
    }

    public function gte(ValueInterface $n): bool
    {
        return $this->value >= $n->value;
    }

    public function isDivisibleBy(ValueInterface $n): bool
    {
        return ! ($n->value === 0) && $this->value % $n->value === 0;
    }

    public function isEqual(ValueInterface $n): bool
    {
        return $this->eq($n);
    }

    public function isEven(): bool
    {
        return $this->value % 2 === 0;
    }

    public function isGreaterThan(ValueInterface $n): bool
    {
        return $this->gt($n);
    }

    public function isGreaterThanOrEqual(ValueInterface $n): bool
    {
        return $this->gte($n);
    }

    public function isLessThan(ValueInterface $n): bool
    {
        return $this->lt($n);
    }

    public function isLessThanOrEqual(ValueInterface $n): bool
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

    public function lt(ValueInterface $n): bool
    {
        return $this->value < $n->value;
    }

    public function lte(ValueInterface $n): bool
    {
        return $this->value <= $n->value;
    }
}