<?php

namespace SSIPG\ValueObjects\Values;

use SSIPG\ValueObjects\Contracts\Equatable;
use SSIPG\ValueObjects\Contracts\ValueInterface;

/** @extends AbstractValue<string> */
class StringValue extends AbstractValue implements Equatable
{
    public static function cast(int|float|string|bool|null $value): string
    {
        return (string) $value;
    }

    /** @param ValueInterface<mixed> $n */
    public function eq(ValueInterface $n): bool
    {
        return $n instanceof self && $this->getValue() === $n->getValue();
    }

    public function setValue(mixed $value): static
    {
        parent::setValue($value);

        $this->value = (string) $this->value;

        return $this;
    }

    public function supports(mixed $value): bool
    {
        return is_string($value) || is_int($value) || is_float($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
