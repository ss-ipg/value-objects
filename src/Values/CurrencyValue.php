<?php

namespace SSIPG\ValueObjects\Values;

use NumberFormatter;

class CurrencyValue extends FloatValue
{
    public string $locale = 'en-US';

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        $this->reformatValue();

        return $this;
    }

    public function toString(): string
    {
        $numberFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $this->precision);

        $formatted = $numberFormatter->format($this->value);

        return $formatted === false
            ? ''
            : $formatted;
    }
}
