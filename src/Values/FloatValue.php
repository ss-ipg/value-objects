<?php

namespace SecureSpace\ValueObjects\Values;

use SecureSpace\ValueObjects\Traits\NumericTrait;

class FloatValue extends AbstractValue
{
    use NumericTrait;

    public int $precision = 2;

    public static function cast($value): float
    {
        return (float) $value;
    }

    public function setPrecision(int $precision): self
    {
        $this->precision = $precision;
        $this->reformatValue();

        return $this;
    }

    public function setValue($value): self
    {   
        parent::setValue($value);

        $this->value = $this->value ? round($this->value, ini_get('precision')) : $this->value;
        $this->setFormattedValue($this->value);

        return $this;
    } 

    public function supports($value): bool
    {
        return is_float($value) || is_int($value);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'precision' => $this->precision,
        ]);
    }

    public function toString(): string
    {
        return number_format($this->value, $this->precision);
    }
}
