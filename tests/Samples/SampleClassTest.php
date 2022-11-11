<?php

namespace Samples;

use Hrpdevtools\TestingTools\Samples\SampleClass;
use Hrpdevtools\TestingTools\UnitTesting\ClassUtilities;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class SampleClassTest extends TestCase
{
    use ClassUtilities;

    public string $className = 'Hrpdevtools\\TestingTools\\Samples\\SampleClass';

    /**
     * @throws ReflectionException
     */
    public function testClassSampleClass()
    {
        $this->utilityToTestClassTraitOrInterface(
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
        $this->utilityToTestClassProperty($this->className,
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
        $this->utilityToTestClassProperty($this->className, 'attrib2', 'public', 'string',
            '2');
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib3()
    {
        $this->utilityToTestClassProperty($this->className, 'attrib3', 'public', 'float',
            3.1, null, false);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib4()
    {
        $this->utilityToTestClassProperty(
            $this->className,
            'attrib4',
            'public',
            'unset',
            null);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib5()
    {
        $this->utilityToTestClassProperty($this->className, 'attrib5', 'protected', 'bool',
            'unset', 1);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib6()
    {
        $this->utilityToTestClassProperty($this->className, 'attrib6', 'private', 'string',
            'unset', 2);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib7()
    {
        $this->utilityToTestClassProperty($this->className, 'attrib7', 'private', 'array',
            'unset', 3);
    }

    /**
     * @throws ReflectionException
     */
    public function testSampleClassPropertyAttrib8()
    {
        $this->utilityToTestClassProperty($this->className, 'attrib8', 'private', 'object',
            'unset', 4);
    }

}