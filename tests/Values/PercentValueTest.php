<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\NullValue;
use SecureSpace\ValueObjects\Values\PercentValue;

class PercentValueTest extends TestCase
{
    public function testFrom(): void
    {
        $percent = PercentValue::from(null);
        $this->assertEquals('', $percent->formatted);
        $this->assertEquals(null, $percent->value);

        $percent = PercentValue::from(0.0);
        $this->assertEquals('0.00%', $percent->formatted);
        $this->assertEquals(0.0, $percent->value);

        $percent = PercentValue::from(1.0);
        $this->assertEquals('100.00%', $percent->formatted);
        $this->assertEquals(1.0, $percent->value);

        $percent = PercentValue::from(0.123456);
        $this->assertEquals('12.35%', $percent->formatted);
        $this->assertEquals(0.123456, $percent->value);

        $percent = PercentValue::from(-0.9876);
        $this->assertEquals('-98.76%', $percent->formatted);
        $this->assertEquals(-0.9876, $percent->value);

        $this->assertEquals(
            expected: 'Increase: 110%',
            actual: PercentValue::from(1.10)
                ->setPrecision(0)
                ->format(fn(PercentValue $p) => "Increase: $p")
        );

        $percent = PercentValue::from(0.10)
            ->setPrecision(0)
            ->formatWith(fn(PercentValue $p) => "$p Off")
            ->toArray();

        $this->assertEquals('10% Off', $percent['formatted']);
    }

    public function testFromFraction(): void
    {
        $percent = PercentValue::fromFraction(1, 3);
        $this->assertEquals('33.33%', $percent->formatted);
        $this->assertEquals(0.3333333333333333, $percent->value);

        $percent = PercentValue::fromFraction(10, 5);
        $this->assertEquals('200.00%', $percent->formatted);
        $this->assertEquals(2.0, $percent->value);

        $percent = PercentValue::fromFraction(10, 21)->setPrecision(5);
        $this->assertEquals('47.61905%', $percent->formatted);
        $this->assertEquals(5, $percent->precision);
        $this->assertEquals(0.47619047619047616, $percent->value);

        $percent = PercentValue::fromFraction(1, 0);
        $this->assertEquals('', $percent->formatted);
        $this->assertNull($percent->value);
    }

    public function testFromFractionWithZeroDenominator(): void
    {
        $percent = PercentValue::fromFraction(1, 0);
        $this->assertInstanceOf(NullValue::class, $percent);

        $percent = PercentValue::fromFraction(1, 0.0);
        $this->assertInstanceOf(NullValue::class, $percent);
    }

    public function testFromWhole(): void
    {
        $percent = PercentValue::fromWhole(0);
        $this->assertEquals('0.00%', $percent->formatted);
        $this->assertEquals(0.0, $percent->value);

        $percent = PercentValue::fromWhole(100);
        $this->assertEquals('100.00%', $percent->formatted);
        $this->assertEquals(1.0, $percent->value);

        $percent = PercentValue::fromWhole(12.3456);
        $this->assertEquals('12.35%', $percent->formatted);
        $this->assertEquals(0.123456, $percent->value);

        $percent = PercentValue::fromWhole(-98.76);
        $this->assertEquals('-98.76%', $percent->formatted);
        $this->assertEquals(-0.9876, $percent->value);

        $this->assertEquals(
            expected: 'Increase: 110%',
            actual: PercentValue::fromWhole(110)
                ->setPrecision(0)
                ->format(fn(PercentValue $p) => "Increase: $p")
        );

        $percent = PercentValue::fromWhole(10)
            ->setPrecision(0)
            ->formatWith(fn(PercentValue $p) => "$p Off")
            ->toArray();

        $this->assertEquals('10% Off', $percent['formatted']);
    }
}
