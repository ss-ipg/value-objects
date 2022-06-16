<?php

namespace SecureSpace\ValueObjects\Tests\Traits;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\FloatValue;
use SecureSpace\ValueObjects\Values\IntegerValue;

class NumericTraitTest extends TestCase
{
    public function testEq(): void
    {
        $this->assertTrue(FloatValue::from(962.0)->eq(FloatValue::from(962.0)));
        $this->assertTrue(FloatValue::from(962.0)->isEqual(FloatValue::from(962.0)));

        $this->assertFalse(FloatValue::from(962.0)->eq(FloatValue::from(269.0)));
        $this->assertFalse(FloatValue::from(962.0)->isEqual(FloatValue::from(269.0)));

        $this->assertFalse(IntegerValue::from(962)->eq(FloatValue::from(962.0)));
        $this->assertFalse(IntegerValue::from(962)->isEqual(FloatValue::from(962.0)));
    }

    public function testGreaterThan(): void
    {
        $this->assertTrue(IntegerValue::from(100)->gt(IntegerValue::from(1)));
        $this->assertTrue(IntegerValue::from(100)->gt(IntegerValue::from(99)));
        $this->assertTrue(IntegerValue::from(100)->gt(FloatValue::from(99.99)));

        $this->assertTrue(IntegerValue::from(100)->isGreaterThan(IntegerValue::from(1)));
        $this->assertTrue(IntegerValue::from(100)->isGreaterThan(IntegerValue::from(99)));
        $this->assertTrue(IntegerValue::from(100)->isGreaterThan(FloatValue::from(99.99)));

        $this->assertFalse(IntegerValue::from(100)->gt(IntegerValue::from(1000)));
        $this->assertFalse(IntegerValue::from(-1)->gt(IntegerValue::from(1)));

        $this->assertFalse(IntegerValue::from(100)->isGreaterThan(IntegerValue::from(1000)));
        $this->assertFalse(IntegerValue::from(-1)->isGreaterThan(IntegerValue::from(1)));
    }

    public function testGte(): void
    {
        $this->assertTrue(IntegerValue::from(100)->gte(IntegerValue::from(1)));
        $this->assertTrue(IntegerValue::from(100)->gte(IntegerValue::from(100)));
        $this->assertTrue(IntegerValue::from(100)->gte(FloatValue::from(100.00)));

        $this->assertTrue(IntegerValue::from(100)->isGreaterThanOrEqual(IntegerValue::from(1)));
        $this->assertTrue(IntegerValue::from(100)->isGreaterThanOrEqual(IntegerValue::from(100)));
        $this->assertTrue(IntegerValue::from(100)->isGreaterThanOrEqual(FloatValue::from(100.00)));
    }

    public function testIsDivisibleBy(): void
    {
        $this->assertTrue(IntegerValue::from(100)->isDivisibleBy(IntegerValue::from(1)));
        $this->assertTrue(IntegerValue::from(100)->isDivisibleBy(IntegerValue::from(2)));
        $this->assertTrue(IntegerValue::from(100)->isDivisibleBy(IntegerValue::from(25)));
        $this->assertTrue(IntegerValue::from(100)->isDivisibleBy(IntegerValue::from(50)));
        $this->assertTrue(IntegerValue::from(100)->isDivisibleBy(IntegerValue::from(100)));

        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(0)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(2)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(3)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(4)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(5)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(6)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(7)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(8)));
        $this->assertFalse(IntegerValue::from(739)->isDivisibleBy(IntegerValue::from(9)));
    }

    public function testIsEven(): void
    {
        $this->assertTrue(IntegerValue::from(2)->isEven());
        $this->assertTrue(IntegerValue::from(2468)->isEven());
        $this->assertTrue(IntegerValue::from(13572)->isEven());

        $this->assertFalse(IntegerValue::from(1)->isEven());
        $this->assertFalse(IntegerValue::from(501)->isEven());
        $this->assertFalse(IntegerValue::from(2461)->isEven());
    }

    public function testIsNegative(): void
    {
        $this->assertFalse(IntegerValue::from(0)->isNegative());
        $this->assertTrue(IntegerValue::from(-2408)->isNegative());
        $this->assertFalse(IntegerValue::from(721)->isNegative());
    }

    public function testIsOdd(): void
    {
        $this->assertTrue(IntegerValue::from(1)->isOdd());
        $this->assertTrue(IntegerValue::from(501)->isOdd());
        $this->assertTrue(IntegerValue::from(2461)->isOdd());

        $this->assertFalse(IntegerValue::from(2)->isOdd());
        $this->assertFalse(IntegerValue::from(2468)->isOdd());
        $this->assertFalse(IntegerValue::from(13572)->isOdd());
    }

    public function testIsPositive(): void
    {
        $this->assertTrue(IntegerValue::from(0)->isPositive());
        $this->assertTrue(IntegerValue::from(21)->isPositive());
        $this->assertFalse(IntegerValue::from(-9)->isPositive());
    }

    public function testIsWhole(): void
    {
        $this->assertTrue(IntegerValue::from(123)->isWhole());
        $this->assertFalse(FloatValue::from(123.00)->isWhole());
    }

    public function testLt(): void
    {
        $this->assertTrue(IntegerValue::from(100)->lt(IntegerValue::from(1000)));
        $this->assertTrue(IntegerValue::from(-1)->lt(IntegerValue::from(1)));

        $this->assertTrue(IntegerValue::from(100)->isLessThan(IntegerValue::from(1000)));
        $this->assertTrue(IntegerValue::from(-1)->isLessThan(IntegerValue::from(1)));

        $this->assertFalse(IntegerValue::from(100)->isLessThan(IntegerValue::from(1)));
        $this->assertFalse(IntegerValue::from(100)->isLessThan(IntegerValue::from(99)));
        $this->assertFalse(IntegerValue::from(100)->isLessThan(FloatValue::from(99.99)));

        $this->assertFalse(IntegerValue::from(100)->isLessThan(IntegerValue::from(1)));
        $this->assertFalse(IntegerValue::from(100)->isLessThan(IntegerValue::from(99)));
        $this->assertFalse(IntegerValue::from(100)->isLessThan(FloatValue::from(99.99)));
    }

    public function testLte(): void
    {
        $this->assertTrue(IntegerValue::from(1)->lte(IntegerValue::from(100)));
        $this->assertTrue(IntegerValue::from(100)->lte(IntegerValue::from(100)));

        $this->assertTrue(IntegerValue::from(1)->isLessThanOrEqual(IntegerValue::from(100)));
        $this->assertTrue(IntegerValue::from(100)->isLessThanOrEqual(IntegerValue::from(100)));
    }

    public function testPrecision(): void
    {
        $number = FloatValue::from(0.123456789);
        $this->assertEquals('0', $number->setPrecision(0)->formatted);
        $this->assertEquals('0.1', $number->setPrecision(1)->formatted);
        $this->assertEquals('0.12', $number->setPrecision(2)->formatted);
        $this->assertEquals('0.123', $number->setPrecision(3)->formatted);
        $this->assertEquals('0.1235', $number->setPrecision(4)->formatted);
        $this->assertEquals('0.12346', $number->setPrecision(5)->formatted);
        $this->assertEquals('0.123457', $number->setPrecision(6)->formatted);
        $this->assertEquals('0.1234568', $number->setPrecision(7)->formatted);
        $this->assertEquals('0.12345679', $number->setPrecision(8)->formatted);
        $this->assertEquals('0.123456789', $number->setPrecision(9)->formatted);
    }
}
