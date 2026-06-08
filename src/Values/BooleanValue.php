<?php

namespace SSIPG\ValueObjects\Values;

use SSIPG\ValueObjects\Contracts\Equatable;
use SSIPG\ValueObjects\Contracts\ValueInterface;

/** @extends AbstractValue<bool> */
class BooleanValue extends AbstractValue implements Equatable
{
    public static function cast(int|float|string|bool|null $value): bool
    {
        return (bool) $value;
    }

    /** @param ValueInterface<mixed> $n */
    public function eq(ValueInterface $n): bool
    {
        return $n instanceof self && $this->getValue() === $n->getValue();
    }

    public function setValue(mixed $value): static
    {
        parent::setValue($value);

        $this->value = (bool) $this->value;

        return $this;
    }

    public function supports(mixed $value): bool
    {
        return is_bool($value) || is_int($value);
    }

    public function toString(): string
    {
        return $this->getValue() ? 'true' : 'false';
    }
}
