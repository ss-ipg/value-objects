<?php

namespace SSIPG\ValueObjects\Tests\Testing;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Values\CurrencyValue;
use SSIPG\ValueObjects\Values\FloatValue;

class ValueComparatorTest extends TestCase
{
    public function testAssertEqualsRoutesThroughEq(): void
    {
        // Would fail under ObjectComparator's property-by-property comparison;
        // passes under ValueComparator because FloatValue::eq() absorbs ULP noise.
        $this->assertEquals(
            FloatValue::from(15.0),
            FloatValue::from(14.99999999999999),
        );
    }

    public function testCurrencyValueUlpNoiseAbsorbed(): void
    {
        $this->assertEquals(
            CurrencyValue::from(-80.81),
            CurrencyValue::from(-80.81000000000002),
        );
    }

    public function testValuesOutsideEpsilonFail(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertEquals(
            FloatValue::from(1.0),
            FloatValue::from(1.01),
        );
    }
}
