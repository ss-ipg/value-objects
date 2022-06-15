<?php

namespace SecureSpace\ValueObjects;

class InfinityValue extends AbstractValue
{
    public static function from($value): static
    {
        return new self(INF);
    }

    public function setValue($value): self
    {
        $this->value = INF;

        return $this;
    }

    /** @codeCoverageIgnore */
    public function supports($value): bool
    {
        return is_infinite($value);
    }

    public function toString(): string
    {
        return INF;
    }
}
