<?php

namespace SecureSpace\ValueObjects;

class BooleanValue extends AbstractValue
{
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
