<?php

namespace Samples;

use Hrpdevtools\TestingTools\Samples\SampleTrait;
use Hrpdevtools\TestingTools\UnitTesting\ClassUtilities;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class SampleTraitTest extends TestCase
{
    use ClassUtilities;

    /**
     * @throws ReflectionException
     */
    public function testSampleTrait()
    {
        $this->utilityTestClassTraitOrInterface(
            'Hrpdevtools\\TestingTools\\Samples\\SampleTrait',
            'Hrpdevtools\\TestingTools\\Samples',
            [],
            [],
            false,
            false,
            false,
            false,
            false,
            true);
    }
}