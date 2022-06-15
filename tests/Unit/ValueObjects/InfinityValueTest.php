<?php

namespace SecureSpace\Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\InfinityValue;

class InfinityValueTest extends TestCase
{
    /** Infinity, regardless of what's given, can only ever be infinity. */
    public function testFrom(): void
    {
        foreach ([null, 0, 1.23, true, false, 'string'] as $value) {
            $infinity = InfinityValue::from($value);
            $this->assertEquals(INF, $infinity->formatted);
            $this->assertEquals(INF, $infinity->value);
        }
    }
}
