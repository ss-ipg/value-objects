<?php

namespace SSIPG\ValueObjects\Contracts;

/**
 * Marker for value objects that define their own equality semantics
 * (typically tolerance-aware for floats, identity-based for everything else).
 */
interface Equatable extends \Stringable
{
    /** @param ValueInterface<mixed> $n */
    public function eq(ValueInterface $n): bool;
}
