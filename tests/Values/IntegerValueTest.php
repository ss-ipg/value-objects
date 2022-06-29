<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\IntegerValue;

class IntegerValueTest extends TestCase
{
    public function testCast(): void
    {
        $this->assertEquals(1, IntegerValue::cast(1));
        $this->assertEquals(1, IntegerValue::cast(1.0));
        $this->assertEquals(null, IntegerValue::cast(null));
        $this->assertEquals(null, IntegerValue::cast('foo'));
    }
}
