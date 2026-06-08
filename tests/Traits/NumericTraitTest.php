<?php

namespace SSIPG\ValueObjects\Tests\Traits;

use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Contracts\ValueInterface;
use SSIPG\ValueObjects\Values\BooleanValue;
use SSIPG\ValueObjects\Values\CurrencyValue;
use SSIPG\ValueObjects\Values\FloatValue;
use SSIPG\ValueObjects\Values\IntegerValue;
use SSIPG\ValueObjects\Values\StringValue;

class NumericTraitTest extends TestCase
{
    public function testAdd(): void
    {
        $this->assertExact(IntegerValue::from(5)->add(IntegerValue::from(3)), IntegerValue::class, 8);
        $this->assertExact(IntegerValue::from(5)->add(FloatValue::from(2.5)), FloatValue::class, 7.5);
        $this->assertExact(IntegerValue::from(5)->add(FloatValue::from(3.0)), FloatValue::class, 8.0);
        $this->assertExact(FloatValue::from(5.0)->add(IntegerValue::from(3)), FloatValue::class, 8.0);
        $this->assertExact(FloatValue::from(1.5)->add(FloatValue::from(2.5)), FloatValue::class, 4.0);
        $this->assertExact(CurrencyValue::from(10.0)->add(CurrencyValue::from(3.0)), CurrencyValue::class, 13.0);
        $this->assertExact(CurrencyValue::from(10.0)->add(FloatValue::from(3.0)), CurrencyValue::class, 13.0);
        $this->assertExact(CurrencyValue::from(10.0)->add(IntegerValue::from(3)), CurrencyValue::class, 13.0);

        // Receiver wins: int + currency loses currency-ness.
        $this->assertExact(IntegerValue::from(5)->add(CurrencyValue::from(3.0)), FloatValue::class, 8.0);
    }

    public function testSubtract(): void
    {
        $this->assertExact(IntegerValue::from(10)->subtract(IntegerValue::from(3)), IntegerValue::class, 7);
        $this->assertExact(IntegerValue::from(10)->subtract(FloatValue::from(2.5)), FloatValue::class, 7.5);
        $this->assertExact(FloatValue::from(5.0)->subtract(IntegerValue::from(3)), FloatValue::class, 2.0);
        $this->assertExact(CurrencyValue::from(10.0)->subtract(IntegerValue::from(3)), CurrencyValue::class, 7.0);
    }

    public function testMultiply(): void
    {
        $this->assertExact(IntegerValue::from(4)->multiply(IntegerValue::from(3)), IntegerValue::class, 12);
        $this->assertExact(IntegerValue::from(4)->multiply(FloatValue::from(2.5)), FloatValue::class, 10.0);
        $this->assertExact(FloatValue::from(2.0)->multiply(IntegerValue::from(3)), FloatValue::class, 6.0);
        $this->assertExact(CurrencyValue::from(10.0)->multiply(IntegerValue::from(3)), CurrencyValue::class, 30.0);
    }

    public function testDivide(): void
    {
        // Clean int division stays int (PHP: 10/5 returns int 2).
        $this->assertExact(IntegerValue::from(10)->divide(IntegerValue::from(5)), IntegerValue::class, 2);

        // Non-whole int division promotes (PHP: 10/3 returns float).
        $result = IntegerValue::from(10)->divide(IntegerValue::from(3));
        $this->assertSame(FloatValue::class, $result::class);
        $this->assertEqualsWithDelta(10 / 3, $result->getValue(), 0.0000001);
        $this->assertExact(FloatValue::from(5.0)->divide(IntegerValue::from(2)), FloatValue::class, 2.5);
        $this->assertExact(CurrencyValue::from(10.0)->divide(IntegerValue::from(2)), CurrencyValue::class, 5.0);
    }

    public function testDivisionByZeroThrows(): void
    {
        $this->expectException(\DivisionByZeroError::class);

        IntegerValue::from(10)->divide(IntegerValue::from(0));
    }

    public function testGteWithinEpsilonIsTrueBothDirections(): void
    {
        $a = FloatValue::from(15.0);
        $b = FloatValue::from(14.99999999999999);

        $this->assertTrue($a->gte($b));
        $this->assertTrue($b->gte($a));
    }

    public function testGtWithinEpsilonIsFalseBothDirections(): void
    {
        $a = FloatValue::from(15.0);
        $b = FloatValue::from(14.99999999999999);

        $this->assertFalse($a->gt($b));
        $this->assertFalse($b->gt($a));
    }

    public function testLteWithinEpsilonIsTrueBothDirections(): void
    {
        $a = FloatValue::from(15.0);
        $b = FloatValue::from(14.99999999999999);

        $this->assertTrue($a->lte($b));
        $this->assertTrue($b->lte($a));
    }

    public function testLtWithinEpsilonIsFalseBothDirections(): void
    {
        $a = FloatValue::from(15.0);
        $b = FloatValue::from(14.99999999999999);

        $this->assertFalse($a->lt($b));
        $this->assertFalse($b->lt($a));
    }

    public function testEq(): void
    {
        $this->assertTrue(FloatValue::from(962.0)->eq(FloatValue::from(962.0)));
        $this->assertTrue(FloatValue::from(962.0)->isEqual(FloatValue::from(962.0)));

        $this->assertFalse(FloatValue::from(962.0)->eq(FloatValue::from(269.0)));
        $this->assertFalse(FloatValue::from(962.0)->isEqual(FloatValue::from(269.0)));

        // Cross-type numeric equality: 962 (int) and 962.0 (float) are equal in value.
        $this->assertTrue(IntegerValue::from(962)->eq(FloatValue::from(962.0)));
        $this->assertTrue(IntegerValue::from(962)->isEqual(FloatValue::from(962.0)));

        // ULP noise within declared precision is absorbed (PHP 8.4+ round() behaviour).
        $this->assertTrue(FloatValue::from(15.0)->eq(FloatValue::from(14.99999999999999)));
        $this->assertTrue(CurrencyValue::from(-80.81)->eq(CurrencyValue::from(-80.81000000000002)));

        // Values clearly outside epsilon are still unequal.
        $this->assertFalse(FloatValue::from(1.0)->eq(FloatValue::from(1.01)));

        // Cross-type guard: non-numeric operands always return false even though their
        // (float) coercion would land within epsilon (true -> 1.0, false -> 0.0).
        $this->assertFalse(FloatValue::from(1.0)->eq(BooleanValue::from(true)));
        $this->assertFalse(FloatValue::from(0.0)->eq(BooleanValue::from(false)));
        $this->assertFalse(IntegerValue::from(1)->eq(StringValue::from('1')));
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

    public function testIsNegative(): void
    {
        $this->assertFalse(IntegerValue::from(0)->isNegative());
        $this->assertTrue(IntegerValue::from(-2_408)->isNegative());
        $this->assertFalse(IntegerValue::from(721)->isNegative());
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
        $this->assertTrue(FloatValue::from(123.00)->isWhole());
        $this->assertTrue(FloatValue::from(-7.0)->isWhole());
        $this->assertTrue(FloatValue::from(0.0)->isWhole());

        $this->assertFalse(FloatValue::from(1.5)->isWhole());
        $this->assertFalse(FloatValue::from(-0.25)->isWhole());
        $this->assertFalse(FloatValue::from(INF)->isWhole());
    }

    public function testLt(): void
    {
        $this->assertTrue(IntegerValue::from(100)->lt(IntegerValue::from(1_000)));
        $this->assertTrue(IntegerValue::from(-1)->lt(IntegerValue::from(1)));

        $this->assertTrue(IntegerValue::from(100)->isLessThan(IntegerValue::from(1_000)));
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

    /** @param ValueInterface<int|float> $result */
    private function assertExact(ValueInterface $result, string $class, int|float $value): void
    {
        $this->assertSame($class, $result::class);
        $this->assertSame($value, $result->getValue());
    }
}
