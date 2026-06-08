<?php

namespace SSIPG\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Values\IntegerValue;
use SSIPG\ValueObjects\Values\StringValue;

class StringValueTest extends TestCase
{
    public function testEq(): void
    {
        $this->assertTrue(StringValue::from('a')->eq(StringValue::from('a')));
        $this->assertFalse(StringValue::from('a')->eq(StringValue::from('b')));

        // Cross-type comparisons are never equal, e.g. IntegerValue::from(1) and StringValue::from('1') are distinct.
        $this->assertFalse(StringValue::from('1')->eq(IntegerValue::from(1)));
    }

    public function testCast(): void
    {
        $this->assertEquals('foo', StringValue::cast('foo'));
        $this->assertEquals('FOO', StringValue::cast('FOO'));
        $this->assertEquals('123', StringValue::cast(123));
        $this->assertEquals('123', StringValue::cast(123.00));
        $this->assertEquals('', StringValue::cast(null));
        $this->assertEquals('1', StringValue::cast(true));
        $this->assertEquals('', StringValue::cast(false));
    }

    public function testFrom(): void
    {
        $this->assertNull(StringValue::from(null));

        $string = StringValue::from('Hello World!');
        $this->assertEquals('Hello World!', $string->formatted);
        $this->assertEquals('Hello World!', $string->value);

        $string = StringValue::from(1234);
        $this->assertEquals('1234', $string->formatted);
        $this->assertEquals('1234', $string->value);
    }
}
