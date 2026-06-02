<?php

namespace SSIPG\ValueObjects\Contracts;

/** @template-covariant TValue */
interface ValueInterface extends \Stringable
{
    /** @return TValue */
    public function getValue(): mixed;
}
