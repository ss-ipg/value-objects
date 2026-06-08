<?php

namespace SSIPG\ValueObjects\Testing;

use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ObjectComparator;
use SSIPG\ValueObjects\Contracts\Equatable;
use SSIPG\ValueObjects\Contracts\ValueInterface;

/**
 * Routes PHPUnit's assertEquals on value-object pairs through Equatable::eq()
 * instead of falling back to ObjectComparator's property-by-property deep
 * comparison. Eliminates ULP-level float fragility under PHP 8.4+.
 */
final class ValueComparator extends ObjectComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof Equatable
            && $actual instanceof ValueInterface;
    }

    public function assertEquals(
        mixed $expected,
        mixed $actual,
        float $delta = 0.0,
        bool $canonicalize = false,
        bool $ignoreCase = false,
        array &$processed = [],
    ): void {
        // PHPUnit's Comparator contract: assertEquals() is only invoked after accepts()
        // returns true, so at runtime these are guaranteed to be the right types. The
        // parent ObjectComparator::assertEquals signature is mixed/mixed though, so we
        // re-narrow here for PHPStan's benefit. Throwing (rather than silently passing
        // or returning) makes it loud if PHPUnit ever changes that contract.
        if (! $expected instanceof Equatable || ! $actual instanceof ValueInterface) {
            throw new \LogicException(
                'ValueComparator::assertEquals reached without satisfying accepts().',
            );
        }

        if ($expected->eq($actual)) {
            return;
        }

        $expectedString = (string) $expected;
        $actualString = (string) $actual;

        throw new ComparisonFailure(
            expected: $expected,
            actual: $actual,
            expectedAsString: $expectedString,
            actualAsString: $actualString,
            message: sprintf('Failed asserting that %s is equal to %s.', $actualString, $expectedString),
        );
    }
}
