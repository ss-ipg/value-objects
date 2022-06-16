<?php

namespace SecureSpace\ValueObjects\Values;

use SecureSpace\ValueObjects\Exceptions\UnsupportedValueType;

abstract class AbstractValue implements ValueInterface
{
    /** Human-readable readable string representing the value of the value.  */
    public string $formatted;

    public \Closure | null $formatter;

    public mixed $value;

    public function __construct($value)
    {
        $this->setValue($value);
        $this->setFormattedValue($value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /** Convert the value object to a human-readable string when self::$formatter is `null`. */
    abstract public function toString(): string;

    abstract public function supports($value): bool;

    /** Cast the value into the required type. */
    abstract public static function cast($value): mixed;

    public static function from($value): static | NullValue
    {
        return is_null($value)
            ? new NullValue(null)
            : new static($value);
    }

    public function format(\callable | \Closure | null $formatter = null): string
    {
        $this->formatter = $formatter;

        return is_callable($formatter)
            ? $formatter($this)
            : $this->toString();
    }

    public function formatWith(\callable | \Closure $formatter): self
    {
        $this->formatter = $formatter;
        $this->formatted = $formatter($this);

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function reformatValue(): self
    {
        $this->formatted = $this->setFormattedValue($this->value);

        return $this;
    }

    public function setFormattedValue($value): self
    {
        $this->formatted = ! is_null($value)
            ? $this->format()
            : '';

        return $this;
    }

    /** @throws UnsupportedValueType */
    public function setValue($value): self
    {
        if (! $this->supports($value)) {
            throw new UnsupportedValueType(sprintf('%s does not support values of type `%s`.',
                get_class($this),
                gettype($value)
            ));
        }

        $this->value = $value;
        $this->setFormattedValue($this->value);

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'formatted' => $this->formatted,
            'value' => $this->value,
        ];
    }
}