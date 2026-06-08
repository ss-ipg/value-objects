<?php

namespace SSIPG\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Values\BooleanValue;
use SSIPG\ValueObjects\Values\IntegerValue;
use SSIPG\ValueObjects\Values\StringValue;

class BooleanValueTest extends TestCase
{
    public function testEq(): void
    {
        $this->assertTrue(BooleanValue::from(true)->eq(BooleanValue::from(true)));
        $this->assertTrue(BooleanValue::from(false)->eq(BooleanValue::from(false)));
        $this->assertFalse(BooleanValue::from(true)->eq(BooleanValue::from(false)));

        // Cross-type comparisons are never equal.
        $this->assertFalse(BooleanValue::from(true)->eq(IntegerValue::from(1)));
        $this->assertFalse(BooleanValue::from(false)->eq(StringValue::from('')));
    }

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
        $this->assertNull(BooleanValue::from(null));

        $boolToSmiley = fn (BooleanValue $bool) => $bool->value ? ':-)' : ':-(';

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
