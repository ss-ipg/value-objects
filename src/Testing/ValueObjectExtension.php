<?php

namespace SSIPG\ValueObjects\Testing;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use SebastianBergmann\Comparator\Factory;

/**
 * Registers the ValueComparator once at suite bootstrap so every assertEquals
 * on a value-object pair routes through Equatable::eq().
 *
 * Required consumer setup in phpunit.xml:
 *
 *     <extensions>
 *         <bootstrap class="SSIPG\ValueObjects\Testing\ValueObjectExtension"/>
 *     </extensions>
 */
final class ValueObjectExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        Factory::getInstance()->register(new ValueComparator);
    }
}
