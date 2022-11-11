<?php

namespace Samples;

use Hrpdevtools\TestingTools\Samples\SampleClass;
use PHPUnit\Framework\TestCase;
use Hrpdevtools\TestingTools\UnitTesting\Utilities;
use ReflectionException;

class SampleClassTest extends TestCase
{
    public string $className = 'Hrpdevtools\\TestingTools\\Samples\\SampleClass';

    /**
     * @throws ReflectionException
     */
    public function testClassSampleClass()
    {
        Utilities::testClass(
            $this->className,
            'Hrpdevtools\\TestingTools\\Samples',
            [],
            [],
            true,
            false,
            true,
            false,
            false,
            false);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib1()
    {
        Utilities::testClassProperty($this->className,
            'attrib1',
            'public',
            'int',
            1,
            null,
            true);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib2()
    {
        Utilities::testClassProperty($this->className, 'attrib2', 'public', 'string',
            '2');
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib3()
    {
        Utilities::testClassProperty($this->className, 'attrib3', 'public', 'float',
            3.1, null, false);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib4()
    {
        Utilities::testClassProperty($this->className, 'attrib4', 'public', 'unset',
            null);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib5()
    {
        Utilities::testClassProperty($this->className, 'attrib5', 'protected', 'bool',
            'unset', 1);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib6()
    {
        Utilities::testClassProperty($this->className, 'attrib6', 'private', 'string',
            'unset', 2);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib7()
    {
        Utilities::testClassProperty($this->className, 'attrib7', 'private', 'array',
            'unset', 3);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib8()
    {
        Utilities::testClassProperty($this->className, 'attrib8', 'private', 'object',
            'unset', 4);
    }

}