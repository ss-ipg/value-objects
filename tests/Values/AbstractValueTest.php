<?php

namespace SSIPG\ValueObjects\Tests\Values;

use PHPUnit\Framework\TestCase;
use SSIPG\ValueObjects\Exceptions\UnsupportedValueType;
use SSIPG\ValueObjects\Values\IntegerValue;

class AbstractValueTest extends TestCase
{
    public function testGetValue(): void
    {
        $number = IntegerValue::from(617);
        $this->assertEquals(617, $number->getValue());

        $number->setValue(821);
        $this->assertEquals(821, $number->getValue());
    }

    public function testSetValue(): void
    {
        $number = IntegerValue::from(721);
        $this->assertEquals(721, $number->value);
        $this->assertEquals('721', $number->formatted);

        $number->setValue(1_021);
        $this->assertEquals(1_021, $number->value);
        $this->assertEquals('1,021', $number->formatted);

        $this->expectException(UnsupportedValueType::class);
        $number->setValue('foo bar');
    }

    public function testToArray(): void
    {
        $number = IntegerValue::from(186_282)->toArray();
        $this->assertEquals(186_282, $number['value']);
        $this->assertEquals('186,282', $number['formatted']);

        $number = IntegerValue::from(1_016)
            ->formatWith(fn (IntegerValue $n) => "Total: $n adventurous anemones")
            ->toArray();

        $this->assertEquals(1_016, $number['value']);
        $this->assertEquals('Total: 1,016 adventurous anemones', $number['formatted']);

        $number = IntegerValue::from(9_876)
            ->formatWith(fn (IntegerValue $i) => 'Prefix: '.number_format(num: $i->value, thousands_separator: '_'))
            ->toArray();

        $this->assertEquals('Prefix: 9_876', $number['formatted']);
    }
}
