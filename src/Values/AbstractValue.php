<?php

namespace SSIPG\ValueObjects\Values;

use Closure;
use SSIPG\ValueObjects\Contracts\ValueInterface;
use SSIPG\ValueObjects\Exceptions\UnsupportedValueType;

/**
 * @template TValue
 *
 * @implements ValueInterface<TValue>
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractValue implements ValueInterface
{
    /** Human-readable readable string representing the value of the value.  */
    public string $formatted;

    /** @var (Closure(static): string)|null */
    public ?Closure $formatter = null;

    /** @var TValue */
    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->setValue($value);
        $this->setFormattedValue($value);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Number of significant digits to use when comparing this value to another.
     *
     * Override in numeric subclasses that carry a declared precision (e.g. FloatValue).
     * Default is PHP_FLOAT_DIG so non-precision-aware types compare effectively strictly.
     */
    public function getComparisonPrecision(): int
    {
        return PHP_FLOAT_DIG;
    }

    /** Convert the value object to a human-readable string when self::$formatter is `null`. */
    abstract public function toString(): string;

    abstract public function supports(mixed $value): bool;

    /** Cast the value into the required type. */
    abstract public static function cast(int|float|string|bool|null $value): mixed;

    /** @phpstan-return ($value is null ? null : static) */
    public static function from(mixed $value): ?static
    {
        return is_null($value)
            ? null
            : new static($value);
    }

    /** @phpstan-param (callable(static): string)|null $formatter */
    public function format(?callable $formatter = null): string
    {
        $this->formatter = $formatter !== null
            ? $formatter(...)
            : null;

        return $this->formatter !== null
            ? ($this->formatter)($this)
            : $this->toString();
    }

    /** @phpstan-param callable(static): string $formatter */
    public function formatWith(callable $formatter): static
    {
        $this->formatter = $formatter(...);
        $this->formatted = ($this->formatter)($this);

        return $this;
    }

    /** @return TValue */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function reformatValue(): static
    {
        $this->setFormattedValue($this->value);

        return $this;
    }

    public function setFormattedValue(mixed $value): static
    {
        $this->formatted = ! is_null($value)
            ? $this->format()
            : '';

        return $this;
    }

    /** @throws UnsupportedValueType */
    public function setValue(mixed $value): static
    {
        if (! $this->supports($value)) {
            throw new UnsupportedValueType(sprintf(
                '%s does not support values of type `%s`.',
                get_class($this),
                gettype($value)
            ));
        }

        $this->value = $value;
        $this->setFormattedValue($this->value);

        return $this;
    }

    /** @return array{value: TValue, formatted: string} */
    public function toArray(): array
    {
        return [
            'formatted' => $this->formatted,
            'value'     => $this->value,
        ];
    }
}
