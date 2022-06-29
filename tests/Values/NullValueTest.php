<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\IntegerValue;
use SecureSpace\ValueObjects\Values\NullValue;

class NullValueTest extends TestCase
{
    public function testCast(): void
    {
        $this->assertNull(NullValue::cast(null));
        $this->assertNull(NullValue::cast(true));
        $this->assertNull(NullValue::cast(false));
        $this->assertNull(NullValue::cast(123));
        $this->assertNull(NullValue::cast(123.45));
        $this->assertNull(NullValue::cast(INF));
        $this->assertNull(NullValue::cast(['foo' => 'bar']));
        $this->assertNull(NullValue::cast(IntegerValue::from(123)));
    }

    public function testFrom(): void
    {
        $this->assertEquals(new NullValue(), NullValue::from(null));
        $this->assertEquals(new NullValue(), NullValue::from(true));
        $this->assertEquals(new NullValue(), NullValue::from(false));
        $this->assertEquals(new NullValue(), NullValue::from(123));
        $this->assertEquals(new NullValue(), NullValue::from(123.45));
        $this->assertEquals(new NullValue(), NullValue::from(INF));
        $this->assertEquals(new NullValue(), NullValue::from(['foo' => 'bar']));
        $this->assertEquals(new NullValue(), NullValue::from(IntegerValue::from(123)));
    }

    public function testToString(): void
    {
        $this->assertEquals('null', (string) NullValue::from(null));
        $this->assertEquals('null', (string) NullValue::from(true));
        $this->assertEquals('null', (string) NullValue::from(false));
        $this->assertEquals('null', (string) NullValue::from(123));
        $this->assertEquals('null', (string) NullValue::from(123.45));
        $this->assertEquals('null', (string) NullValue::from(INF));
        $this->assertEquals('null', (string) NullValue::from(['foo' => 'bar']));
        $this->assertEquals('null', (string) NullValue::from(IntegerValue::from(123)));
    }
}
