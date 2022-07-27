<?php

namespace SecureSpace\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SecureSpace\ValueObjects\Values\FloatValue;
use SecureSpace\ValueObjects\Values\NullValue;

class FloatValueTest extends TestCase
{
    public function testCast(): void
    {
        $this->assertEquals(1.0, FloatValue::cast(1));
        $this->assertEquals(1.0, FloatValue::cast(1.0));
        $this->assertEquals(null, FloatValue::cast(null));
    }

    public function testFrom(): void
    {
        $float = FloatValue::from(null);
        $this->assertEquals(NullValue::class, get_class($float));

        $float = FloatValue::from(0.0);
        $this->assertEquals('0.00', $float->formatted);
        $this->assertEquals(0.0, $float->value);

        $float = FloatValue::from(1.0);
        $this->assertEquals('1.00', $float->formatted);
        $this->assertEquals(1.0, $float->value);

        $float = FloatValue::from(0.123456)->setPrecision(4);
        $this->assertEquals('0.1235', $float->formatted);
        $this->assertEquals(0.123456, $float->value);

        $float = FloatValue::from(-98.76);
        $this->assertEquals('-98.76', $float->formatted);
        $this->assertEquals(-98.76, $float->value);

        $float = FloatValue::from(123);
        $this->assertEquals('123.00', $float->formatted);
        $this->assertEquals(123.00, $float->value);

        $this->assertEquals(
            expected: 'Pi: 3.14159',
            actual: FloatValue::from(3.14159)
                ->setPrecision(5)
                ->format(fn(FloatValue $p) => "Pi: $p")
        );

        $float = FloatValue::from(5.65)
            ->setPrecision(2)
            ->formatWith(fn(FloatValue $p) => "pH: $p")
            ->toArray()
        ;
        $this->assertEquals('pH: 5.65', $float['formatted']);
    }

    public function testPrecision(): void
    {
        $float = FloatValue::from(0.123456789);
        $this->assertEquals('0', $float->setPrecision(0)->formatted);
        $this->assertEquals('0.1', $float->setPrecision(1)->formatted);
        $this->assertEquals('0.12', $float->setPrecision(2)->formatted);
        $this->assertEquals('0.123', $float->setPrecision(3)->formatted);
        $this->assertEquals('0.1235', $float->setPrecision(4)->formatted);
        $this->assertEquals('0.12346', $float->setPrecision(5)->formatted);
        $this->assertEquals('0.123457', $float->setPrecision(6)->formatted);
        $this->assertEquals('0.1234568', $float->setPrecision(7)->formatted);
        $this->assertEquals('0.12345679', $float->setPrecision(8)->formatted);
        $this->assertEquals('0.123456789', $float->setPrecision(9)->formatted);
    }

    public function testToArray(): void
    {
        $expected = [
            'formatted' => '0.123',
            'precision' => 3,
            'value' => 0.123456,
        ];

        $this->assertEquals($expected, FloatValue::from(0.123456)->setPrecision(3)->toArray());
    }
}
