<?php

namespace SecureSpace\ValueObjects\Values;

class NullValue extends AbstractValue
{
    public static function cast($value): mixed
    {
        return null;
    }

    public static function from($value): static
    {
        return new self(null);
    }

    public function setValue($value): self
    {
        $this->value = null;

        return $this;
    }

    /** @codeCoverageIgnore */
    public function supports($value): bool
    {
        return is_null($value);
    }

    public function toString(): string
    {
        return 'null';
    }
}
