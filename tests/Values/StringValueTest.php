<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\NullValue;
use SecureSpace\ValueObjects\Values\StringValue;

class StringValueTest extends TestCase
{
    public function testFrom(): void
    {
        $string = StringValue::from(null);
        $this->assertEquals(NullValue::class, get_class($string));

        $string = StringValue::from('Hello World!');
        $this->assertEquals('Hello World!', $string->formatted);
        $this->assertEquals('Hello World!', $string->value);

        $string = StringValue::from(1234);
        $this->assertEquals('1234', $string->formatted);
        $this->assertEquals('1234', $string->value);
    }
}
