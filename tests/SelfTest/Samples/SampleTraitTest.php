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
        $this->utility_test_class_interface_or_trait(
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