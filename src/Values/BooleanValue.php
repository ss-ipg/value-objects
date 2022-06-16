<?php

namespace SecureSpace\ValueObjects\Values;

class BooleanValue extends AbstractValue
{
    public static function cast($value): bool
    {
        return (bool) $value;
    }

    public function setValue($value): self
    {
        $this->value = is_null($value) ? null : (bool) $value;

        return $this;
    }

    /** @codeCoverageIgnore */
    public function supports($value): bool
    {
        return is_bool($value);
    }

    public function toString(): string
    {
        return $this->value ? 'true' : 'false';
    }
}
