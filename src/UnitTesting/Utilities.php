<?php

namespace Hrpdevtools\TestingTools\UnitTesting;

use Error;
use PHPUnit\Framework\TestCase as TestCase;
use ReflectionClass;
use ReflectionException;

class Utilities
{
    // Keep this function private so class can't initialize
    private function __construct()
    {
        // Keep empty, do not remove or alter.
    }

    /**
     * Test if class exist
     *
     * @param string $classFullName The class name including namespace.
     * @return void
     */
    public static function testClassExists(string $classFullName): void
    {
        TestCase::assertTrue(class_exists($classFullName), 'Class "' . $classFullName .
            '" wasn\'t found.');
    }

    /**
     * @param string $classFullName The class name including namespace.
     * @return void
     * @throws ReflectionException
     */
    public static function testClassIsInstantiable(string $classFullName): void
    {

        $reflectedClass = new ReflectionClass($classFullName);
        TestCase::assertTrue($reflectedClass->isInstantiable(), 'Class "' . $classFullName . '" isn\'t instantiable.');

    }

    /**
     * @param string $classFullName The class name including namespace.
     * @return void
     * @throws ReflectionException
     */
    public static function testClassIsInterface(string $classFullName): void
    {

        $reflectedClass = new ReflectionClass($classFullName);
        TestCase::assertTrue($reflectedClass->isInterface(), 'Class "' . $classFullName . '" isn\'t interface.');

    }

    /**
     * @param string $classFullName The class name including namespace.
     * @return void
     * @throws ReflectionException
     */
    public static function testClassIsTrait(string $classFullName): void
    {

        $reflectedClass = new ReflectionClass($classFullName);
        TestCase::assertTrue($reflectedClass->isTrait(), 'Class "' . $classFullName . '" isn\'t trait.');

    }

    /**
     * @param string $classFullName The class name including namespace.
     * @return void
     * @throws ReflectionException
     */
    public static function testClassIsAbstract(string $classFullName): void
    {

        $reflectedClass = new ReflectionClass($classFullName);
        TestCase::assertTrue($reflectedClass->isAbstract(), 'Class "' . $classFullName . '" isn\'t abstract.');

    }

    /**
     * Test class property
     *
     * @param string $classFullName The property's class name including namespace.
     * @param string $propertyName The name of the property to test.
     * @param string $propertyAccessibility The kind of property's accessibility ['public'|'private'|'protected'].
     * @param string $propertyValueType The property's value type, use 'unset' for mixed.
     * @param string $propertyDefaultValue The property's default value, use 'unset' for no default.
     * @param int|null $positionInConstructor The property's position in __constructor, use null for no position.
     * @param bool $isStatic State if the property is static, true or false.
     *
     * @return void
     * @throws ReflectionException
     */
    public static function testClassProperty(string $classFullName, string $propertyName, string $propertyAccessibility = 'public',
                                             string $propertyValueType = 'unset', $propertyDefaultValue = 'unset',
                                             ?int   $positionInConstructor = null, bool $isStatic = false): void
    {
        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($propertyAccessibility != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in class
        TestCase::assertTrue($reflectedClass->hasProperty($propertyName),
            'Class property "' . $propertyName . '" isn\'t available in class.');

        // Test is property is static
        if ($reflectedProp->isStatic()) {
            TestCase::assertTrue($isStatic,
                'Class property "' . $propertyName . '" isn\'t static.');
        } else {
            TestCase::assertFalse($isStatic, 'Class property "' . $propertyName . '" is set as static.');
        }

        // Test property accessibility
        $customFailMessage = 'Class property "' . $propertyName . '" accessibility isn\'t ' . $propertyAccessibility . '.';
        switch ($propertyAccessibility) {
            case 'public' :
                TestCase::assertTrue($reflectedProp->isPublic(), $customFailMessage);
                break;
            case 'private':
                TestCase::assertTrue($reflectedProp->isPrivate(), $customFailMessage);
                break;
            case 'protected':
                TestCase::assertTrue($reflectedProp->isProtected(), $customFailMessage);
                break;
            default:
                TestCase::fail('Class property "' . $propertyName . '" accessibility is unconfirmed.');
        }

        // Test property type
        if ($reflectedProp->hasType()) {
            $actualType = $reflectedProp->getType()->getName();
        } else {
            $actualType = 'unset';
        }
        if ($actualType === 'unset' && $propertyValueType === 'mixed') {
            $actualType = 'mixed';
        }
        TestCase::assertSame($propertyValueType, $actualType,
            'Class property "' . $propertyName . '" type isn\'t ' . $propertyValueType . '.');

        if (is_null($positionInConstructor)) {

            //Test property default value
            if (version_compare(PHP_VERSION, '8.0.0') >= 0) {

                // Simple way in PHP 8.0.0 or higher
                if ($reflectedProp->hasDefaultValue()) {
                    $actualDefault = $reflectedProp->getDefaultValue();
                } else {
                    $actualDefault = "unset";
                }

            } else {

                // For previous PHP versions use this longer way
                if ($reflectedProp->isStatic()) {
                    $actualDefault = $reflectedProp->getValue();
                } else {
                    $newObject = $reflectedClass->newInstanceWithoutConstructor();
                    if ($reflectedProp->isInitialized($newObject)) {
                        $actualDefault = $reflectedProp->getValue($newObject);
                        if (is_null($actualDefault) && $propertyDefaultValue === 'unset') {
                            $actualDefault = 'unset';
                        }
                    } else {
                        $actualDefault = 'unset';
                    }
                }
            }
            TestCase::assertSame($propertyDefaultValue, $actualDefault,
                'Class property "' . $propertyName . '" default isn\'t ' . $propertyDefaultValue . '.');

        } else {

            // Test for inconsistencies
            TestCase::assertTrue($propertyDefaultValue === 'unset', 'Class property "' . $propertyName .
                '" shouldn\'t have a default value if it\'s initialized by the constructor.');

            TestCase::assertGreaterThanOrEqual(1, $positionInConstructor, 'Class property "' . $propertyName .
                '" parameter "positionInConstructor" is wrong, position should be 1 or greater. Please review your test configuration.');

            // Test property position in constructor
            $expectedValue = '2.2';
            if ($propertyValueType !== 'unset' && $propertyValueType !== 'mixed') {
                try {
                    settype($expectedValue, $propertyValueType);
                } catch (Error $e) {
                    TestCase::fail('We were not able to calculate expected value for class property "' . $propertyName .
                        '" with type ' . $propertyValueType . '.');
                }
            }

            // Test if constructor exist
            $classConstructor = $reflectedClass->getConstructor();
            TestCase::assertFalse(is_null($classConstructor),
                'There is no constructor for class property "' . $propertyName . '".');

            // Generate array of constructors params
            $constructorParams = $classConstructor->getParameters();
            $classConstructorParams = [];
            foreach ($constructorParams as $param) {
                $paramValue = '0';
                if ($param->hasType()) {
                    $type = $param->getType()->getName();
                    settype($paramValue, $type);
                }
                $classConstructorParams[] = $paramValue;
            }

            try {
                $classConstructorParams[$positionInConstructor - 1] = $expectedValue;
            } catch (Error $e) {
                TestCase::fail('Class property "' . $propertyName .
                    '" parameter "positionInConstructor" is wrong, position should be from 1 to n parameters. Please review your test configuration.');
            }

            try {
                $newObject = $reflectedClass->newInstanceArgs($classConstructorParams);
            } catch (Error $e) {
                TestCase::fail('There is a mismatch between the constructor parameters types and the class properties types.');
            }

            if ($reflectedProp->isStatic()) {
                $actualValue = $reflectedProp->getValue();
            } else {
                $actualValue = $reflectedProp->getValue($newObject);
            }

            TestCase::assertSame($expectedValue, $actualValue,
                'Class property "' . $propertyName . '" positionInConstructor isn\'t ' . $positionInConstructor . '.');

        }

    }
}