<?php

namespace SSIPG\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Exceptions\UnsupportedValueType;
use SSIPG\ValueObjects\Values\IntegerValue;

class IntegerValueTest extends TestCase
{
    public function testCast(): void
    {
        $this->assertEquals(1, IntegerValue::cast(1));
        $this->assertEquals(1, IntegerValue::cast(1.0));
        $this->assertEquals(null, IntegerValue::cast(null));
        $this->assertEquals(null, IntegerValue::cast('foo'));
    }

    public function testFromAcceptsWholeNumberFloats(): void
    {
        $int = IntegerValue::from(1.0);
        $this->assertSame(1, $int->value);

        $int = IntegerValue::from(-3.0);
        $this->assertSame(-3, $int->value);

        $int = IntegerValue::from(0.0);
        $this->assertSame(0, $int->value);
    }

    public function testFromRejectsFractionalFloats(): void
    {
        $this->expectException(UnsupportedValueType::class);
        IntegerValue::from(1.5);
    }

    public function testFromRejectsInfinity(): void
    {
        $this->expectException(UnsupportedValueType::class);
        IntegerValue::from(INF);
    }

    public function testFromRejectsNaN(): void
    {
        $this->expectException(UnsupportedValueType::class);
        IntegerValue::from(NAN);
    }

    public function testFromAcceptsIntegerString(): void
    {
        $this->assertSame(42, IntegerValue::from('42')->value);
        $this->assertSame(-7, IntegerValue::from('-7')->value);
        $this->assertSame(0, IntegerValue::from('0')->value);
    }

    public function testFromAcceptsWholeNumberNumericString(): void
    {
        $this->assertSame(150, IntegerValue::from('1.5e2')->value);
        $this->assertSame(5, IntegerValue::from('5.0')->value);
    }

    public function testFromRejectsFractionalNumericString(): void
    {
        $this->expectException(UnsupportedValueType::class);

        IntegerValue::from('5.5');
    }

    public function testFromRejectsNonNumericString(): void
    {
        $this->expectException(UnsupportedValueType::class);

        IntegerValue::from('hello');
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
        $this->assertTrue(IntegerValue::from(2_468)->isEven());
        $this->assertTrue(IntegerValue::from(13_572)->isEven());

        $this->assertFalse(IntegerValue::from(1)->isEven());
        $this->assertFalse(IntegerValue::from(501)->isEven());
        $this->assertFalse(IntegerValue::from(2_461)->isEven());
    }

    public function testIsOdd(): void
    {
        $this->assertTrue(IntegerValue::from(1)->isOdd());
        $this->assertTrue(IntegerValue::from(501)->isOdd());
        $this->assertTrue(IntegerValue::from(2_461)->isOdd());

        $this->assertFalse(IntegerValue::from(2)->isOdd());
        $this->assertFalse(IntegerValue::from(2_468)->isOdd());
        $this->assertFalse(IntegerValue::from(13_572)->isOdd());
    }
}
