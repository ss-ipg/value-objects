<?php

namespace SecureSpace\ValueObjects\Values;

use NumberFormatter;

class CurrencyValue extends FloatValue
{
    public string $locale = 'en-US';

    public int $precision = 2;

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        $this->reformatValue();

        return $this;
    }

    public function toString(): string
    {
        $numberFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $this->precision);

        return $numberFormatter->format($this->value);
    }
}
