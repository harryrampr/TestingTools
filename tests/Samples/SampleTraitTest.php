<?php

namespace Samples;

use Hrpdevtools\TestingTools\Samples\SampleTrait;
use Hrpdevtools\TestingTools\UnitTesting\Utilities;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class SampleTraitTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSampleTrait()
    {
        Utilities::testClass(
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