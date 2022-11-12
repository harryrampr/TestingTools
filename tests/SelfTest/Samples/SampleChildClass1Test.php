<?php

namespace SelfTest\Samples;

use ReflectionException;
use TestingTools\Samples\SampleChildClass1;
use PHPUnit\Framework\TestCase;
use TestingTools\UnitTesting\ClassUtilities;

class SampleChildClass1Test extends TestCase
{
    use ClassUtilities;

    public string $className = 'TestingTools\\Samples\\SampleChildClass1';

    /**
     * @throws ReflectionException
     */
    public function testSampleChildClass1()
    {
        $this->utility_test_class_structure($this->className,
            dirname($this->className),
            'SampleClass',
            ['TestingTools\Samples\SampleInterface2']);
    }
}