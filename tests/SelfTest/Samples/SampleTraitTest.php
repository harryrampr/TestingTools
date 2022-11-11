<?php

namespace SelfTest\Samples;

use TestingTools\Samples\SampleTrait;
use TestingTools\UnitTesting\ClassUtilities;
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
        $this->utilityToTestClassTraitOrInterface(
            'TestingTools\\Samples\\SampleTrait',
            'TestingTools\\Samples',
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