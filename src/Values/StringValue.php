<?php

namespace SecureSpace\ValueObjects\Values;

class StringValue extends AbstractValue
{
    public static function cast($value): string
    {
        return (string) $value;
    }

    public function setValue($value): self
    {
        $this->value = is_null($value) ? null : (string) $value;

        return $this;
    }

    /** @codeCoverageIgnore */
    public function supports($value): bool
    {
        return is_string($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
