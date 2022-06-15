<?php

namespace SecureSpace\Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\NumberValue;

class NumberValueTest extends TestCase
{
    public function testEq(): void
    {
        $this->assertTrue(NumberValue::from(962)->eq(NumberValue::from(962)));
        $this->assertTrue(NumberValue::from(962)->isEqual(NumberValue::from(962)));

        $this->assertFalse(NumberValue::from(962)->eq(NumberValue::from(269)));
        $this->assertFalse(NumberValue::from(962)->isEqual(NumberValue::from(269)));

        // The integer 962 is not equal to the float 962.0.
        $this->assertFalse(NumberValue::from(962)->eq(NumberValue::from(962.0)));
        $this->assertFalse(NumberValue::from(962)->isEqual(NumberValue::from(962.0)));
    }

    public function testFrom(): void
    {
        $number = NumberValue::from(null);
        $this->assertEquals('', $number->formatted);
        $this->assertEquals(null, $number->value);

        $number = NumberValue::from(0.0);
        $this->assertEquals('0', $number->formatted);
        $this->assertEquals(0, $number->value);

        $number = NumberValue::from(1.0);
        $this->assertEquals('1', $number->formatted);
        $this->assertEquals(1, $number->value);

        $number = NumberValue::from(0.123456)->setPrecision(4);
        $this->assertEquals('0.1235', $number->formatted);
        $this->assertEquals(0.123456, $number->value);

        $number = NumberValue::from(-98.76);
        $this->assertEquals('-99', $number->formatted);
        $this->assertEquals(-98.76, $number->value);

        $this->assertEquals(
            expected: 'Pi: 3.14159',
            actual: NumberValue::from(3.14159)
                ->setPrecision(5)
                ->format(fn(NumberValue $p) => "Pi: $p")
        );

        $number = NumberValue::from(5.65)
            ->setPrecision(2)
            ->formatWith(fn(NumberValue $p) => "pH: $p")
            ->toArray()
        ;
        $this->assertEquals('pH: 5.65', $number['formatted']);
    }

    public function testGreaterThan(): void
    {
        $this->assertTrue(NumberValue::from(100)->gt(NumberValue::from(null)));
        $this->assertTrue(NumberValue::from(100)->gt(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->gt(NumberValue::from(99.99)));

        $this->assertTrue(NumberValue::from(100)->isGreaterThan(NumberValue::from(null)));
        $this->assertTrue(NumberValue::from(100)->isGreaterThan(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->isGreaterThan(NumberValue::from(99.99)));

        $this->assertFalse(NumberValue::from(null)->gt(NumberValue::from(1)));
        $this->assertFalse(NumberValue::from(100)->gt(NumberValue::from(1000)));
        $this->assertFalse(NumberValue::from(-1)->gt(NumberValue::from(1)));

        $this->assertFalse(NumberValue::from(null)->isGreaterThan(NumberValue::from(1)));
        $this->assertFalse(NumberValue::from(100)->isGreaterThan(NumberValue::from(1000)));
        $this->assertFalse(NumberValue::from(-1)->isGreaterThan(NumberValue::from(1)));
    }

    public function testGte(): void
    {
        $this->assertTrue(NumberValue::from(100)->gte(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->gte(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->gte(NumberValue::from(100.00)));

        $this->assertTrue(NumberValue::from(100)->isGreaterThanOrEqual(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->isGreaterThanOrEqual(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->isGreaterThanOrEqual(NumberValue::from(100.00)));
    }

    public function testIsDivisibleBy(): void
    {
        $this->assertTrue(NumberValue::from(100)->isDivisibleBy(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->isDivisibleBy(NumberValue::from(2)));
        $this->assertTrue(NumberValue::from(100)->isDivisibleBy(NumberValue::from(25)));
        $this->assertTrue(NumberValue::from(100)->isDivisibleBy(NumberValue::from(50)));
        $this->assertTrue(NumberValue::from(100)->isDivisibleBy(NumberValue::from(100)));

        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(0)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(2)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(3)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(4)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(5)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(6)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(7)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(8)));
        $this->assertFalse(NumberValue::from(739)->isDivisibleBy(NumberValue::from(9)));
    }

    public function testIsEven(): void
    {
        $this->assertTrue(NumberValue::from(2)->isEven());
        $this->assertTrue(NumberValue::from(2468)->isEven());
        $this->assertTrue(NumberValue::from(13572)->isEven());

        $this->assertFalse(NumberValue::from(1)->isEven());
        $this->assertFalse(NumberValue::from(501)->isEven());
        $this->assertFalse(NumberValue::from(2461)->isEven());
    }

    public function testIsNegative(): void
    {
        $this->assertFalse(NumberValue::from(null)->isNegative());
        $this->assertFalse(NumberValue::from(0)->isNegative());
        $this->assertTrue(NumberValue::from(-2408)->isNegative());
        $this->assertFalse(NumberValue::from(721)->isNegative());
    }

    public function testIsOdd(): void
    {
        $this->assertTrue(NumberValue::from(1)->isOdd());
        $this->assertTrue(NumberValue::from(501)->isOdd());
        $this->assertTrue(NumberValue::from(2461)->isOdd());

        $this->assertFalse(NumberValue::from(2)->isOdd());
        $this->assertFalse(NumberValue::from(2468)->isOdd());
        $this->assertFalse(NumberValue::from(13572)->isOdd());
    }

    public function testIsPositive(): void
    {
        $this->assertFalse(NumberValue::from(null)->isPositive());
        $this->assertTrue(NumberValue::from(0)->isPositive());
        $this->assertTrue(NumberValue::from(21)->isPositive());
        $this->assertFalse(NumberValue::from(-9)->isPositive());
    }

    public function testIsWhole(): void
    {
        $this->assertTrue(NumberValue::from(123)->isWhole());
        $this->assertFalse(NumberValue::from(123.00)->isWhole());
        $this->assertFalse(NumberValue::from(123.45)->isWhole());
    }

    public function testLt(): void
    {
        $this->assertTrue(NumberValue::from(null)->lt(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->lt(NumberValue::from(1000)));
        $this->assertTrue(NumberValue::from(-1)->lt(NumberValue::from(1)));

        $this->assertTrue(NumberValue::from(null)->isLessThan(NumberValue::from(1)));
        $this->assertTrue(NumberValue::from(100)->isLessThan(NumberValue::from(1000)));
        $this->assertTrue(NumberValue::from(-1)->isLessThan(NumberValue::from(1)));

        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(null)));
        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(1)));
        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(99.99)));

        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(null)));
        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(1)));
        $this->assertFalse(NumberValue::from(100)->isLessThan(NumberValue::from(99.99)));
    }

    public function testLte(): void
    {
        $this->assertTrue(NumberValue::from(1)->lte(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->lte(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->lte(NumberValue::from(100.00)));

        $this->assertTrue(NumberValue::from(1)->isLessThanOrEqual(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->isLessThanOrEqual(NumberValue::from(100)));
        $this->assertTrue(NumberValue::from(100)->isLessThanOrEqual(NumberValue::from(100.00)));
    }

    public function testPrecision(): void
    {
        $number = NumberValue::from(0.123456789);
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
