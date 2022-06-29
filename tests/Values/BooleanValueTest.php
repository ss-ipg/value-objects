<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\BooleanValue;
use SecureSpace\ValueObjects\Values\NullValue;

class BooleanValueTest extends TestCase
{
    public function testCast(): void
    {
        $this->assertTrue(BooleanValue::cast(1));
        $this->assertTrue(BooleanValue::cast(true));
        $this->assertFalse(BooleanValue::cast(0));
        $this->assertFalse(BooleanValue::cast(false));
        $this->assertFalse(BooleanValue::cast(null));
    }

    public function testFrom(): void
    {
        $bool = BooleanValue::from(null);
        $this->assertEquals(NullValue::class, get_class($bool));

        $boolToSmiley = fn(BooleanValue $bool) => $bool->value ? ':-)' : ':-(';

        $bool = BooleanValue::from(true)->formatWith($boolToSmiley);
        $this->assertEquals(':-)', $bool->formatted);
        $this->assertIsCallable($bool->formatter);
        $this->assertTrue($bool->value);

        $bool = BooleanValue::from(1)->formatWith($boolToSmiley);
        $this->assertEquals(':-)', $bool->formatted);
        $this->assertTrue($bool->value);

        $bool = BooleanValue::from(false)->formatWith($boolToSmiley);
        $this->assertEquals(':-(', $bool->formatted);
        $this->assertFalse($bool->value);

        $bool = BooleanValue::from(0)->formatWith($boolToSmiley);
        $this->assertEquals(':-(', $bool->formatted);
        $this->assertFalse($bool->value);
    }
}
