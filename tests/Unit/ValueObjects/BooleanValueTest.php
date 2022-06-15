<?php

namespace SecureSpace\Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\BooleanValue;

class BooleanValueTest extends TestCase
{
    public function testFrom(): void
    {
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

        // Typically value objects will return an empty string for the `formatted` property when null is passed to the
        // constructor. Here, we have a value instead because we are using the $boolToSmiley function defined above.
        // This means that we'll either have a happy/sad face, but never an empty string.
        $bool = BooleanValue::from(null)->formatWith($boolToSmiley);
        $this->assertEquals(':-(', $bool->formatted);
        $this->assertNull($bool->value);

        // For example...here we're not using a custom formatter, so the default empty string is returned instead.
        $bool = BooleanValue::from(null);
        $this->assertEquals('', $bool->formatted);
        $this->assertNull($bool->value);
    }
}
