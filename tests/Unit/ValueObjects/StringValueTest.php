<?php

namespace SecureSpace\Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\StringValue;

class StringValueTest extends TestCase
{
    public function testFrom(): void
    {
        $string = StringValue::from(null);
        $this->assertEquals('', $string->formatted);
        $this->assertEquals(null, $string->value);

        $string = StringValue::from('Hello World!');
        $this->assertEquals('Hello World!', $string->formatted);
        $this->assertEquals('Hello World!', $string->value);

        $string = StringValue::from(1234);
        $this->assertEquals('1234', $string->formatted);
        $this->assertEquals('1234', $string->value);
    }
}
