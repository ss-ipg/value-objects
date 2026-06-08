<?php

namespace SSIPG\ValueObjects\Traits;

use SSIPG\ValueObjects\Contracts\ValueInterface;
use SSIPG\ValueObjects\Values\AbstractValue;
use SSIPG\ValueObjects\Values\FloatValue;

trait NumericTrait
{
    /** @param ValueInterface<int|float> $n */
    public function add(ValueInterface $n): self|FloatValue
    {
        return $this->wrap($this->getValue() + $n->getValue());
    }

    /** @param ValueInterface<int|float> $n */
    public function subtract(ValueInterface $n): self|FloatValue
    {
        return $this->wrap($this->getValue() - $n->getValue());
    }

    /** @param ValueInterface<int|float> $n */
    public function multiply(ValueInterface $n): self|FloatValue
    {
        return $this->wrap($this->getValue() * $n->getValue());
    }

    /** @param ValueInterface<int|float> $n */
    public function divide(ValueInterface $n): self|FloatValue
    {
        return $this->wrap($this->getValue() / $n->getValue());
    }

    /**
     * Precision-aware equality.
     *
     * Two numeric values are equal if their absolute difference is less than half a unit
     * in the last place of the more-restrictive declared precision. This absorbs the ULP
     * noise that surfaces post-arithmetic on platforms where round() no longer launders it
     * (PHP 8.4+).
     *
     * Cross-type comparisons (e.g. against a BooleanValue or StringValue) always return
     * false; the (float) cast would coerce non-numerics to 0.0/1.0 and silently report
     * spurious equality. This guard catches the case at runtime even when PHPStan's
     * type constraint is bypassed.
     *
     * @param  ValueInterface<mixed>  $n
     */
    public function eq(ValueInterface $n): bool
    {
        $other = $n->getValue();

        if (! is_int($other) && ! is_float($other)) {
            return false;
        }

        $precision = min(
            $this->getComparisonPrecision(),
            $n instanceof AbstractValue
                ? $n->getComparisonPrecision()
                : PHP_FLOAT_DIG,
        );

        $epsilon = 10 ** -$precision / 2;

        return abs((float) $this->getValue() - (float) $other) < $epsilon;
    }

    /** @param ValueInterface<int|float> $n */
    public function gt(ValueInterface $n): bool
    {
        return ! $this->eq($n) && $this->getValue() > $n->getValue();
    }

    /** @param ValueInterface<int|float> $n */
    public function gte(ValueInterface $n): bool
    {
        return $this->eq($n) || $this->getValue() > $n->getValue();
    }

    /** @param ValueInterface<int|float> $n */
    public function isEqual(ValueInterface $n): bool
    {
        return $this->eq($n);
    }

    /** @param ValueInterface<int|float> $n */
    public function isGreaterThan(ValueInterface $n): bool
    {
        return $this->gt($n);
    }

    /** @param ValueInterface<int|float> $n */
    public function isGreaterThanOrEqual(ValueInterface $n): bool
    {
        return $this->gte($n);
    }

    /** @param ValueInterface<int|float> $n */
    public function isLessThan(ValueInterface $n): bool
    {
        return $this->lt($n);
    }

    /** @param ValueInterface<int|float> $n */
    public function isLessThanOrEqual(ValueInterface $n): bool
    {
        return $this->lte($n);
    }

    public function isNegative(): bool
    {
        return $this->getValue() < 0;
    }

    public function isPositive(): bool
    {
        return $this->getValue() >= 0;
    }

    public function isWhole(): bool
    {
        $value = (float) $this->getValue();

        return ! is_infinite($value) && ! is_nan($value) && floor($value) === $value;
    }

    /** @param ValueInterface<int|float> $n */
    public function lt(ValueInterface $n): bool
    {
        return ! $this->eq($n) && $this->getValue() < $n->getValue();
    }

    /** @param ValueInterface<int|float> $n */
    public function lte(ValueInterface $n): bool
    {
        return $this->eq($n) || $this->getValue() < $n->getValue();
    }

    protected function wrap(int|float $value): self|FloatValue
    {
        if (is_int($value)) {
            return new static($value);
        }

        return $this instanceof FloatValue
            ? new static($value)
            : new FloatValue($value);
    }
}
