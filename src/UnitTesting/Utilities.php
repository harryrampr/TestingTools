<?php

namespace Hrpdevtools\TestingTools\UnitTesting;

use Error;
use PHPUnit\Framework\TestCase as UnitTesting;
use ReflectionClass;
use ReflectionException;

class Utilities
{
    // Keep this function private so class can't initialize
    private function __construct()
    {
    }

    /**
     * @throws ReflectionException
     */
    public static function testClassProperty($classFullName, string $propertyName, string $propertyAccessibility = "public",
                                             string $propertyValueType = "unset", $propertyDefaultValue = 'unset',
                                             int $positionInConstructor = null)
    {
        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($propertyAccessibility != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in class
        UnitTesting::assertTrue($reflectedClass->hasProperty($propertyName),
            'Object property "' . $propertyName . '" isn\'t available.');

        // Test property accessibility
        $customFailMessage = 'Object property "' . $propertyName . '" accessibility isn\'t ' . $propertyAccessibility . '.';
        switch ($propertyAccessibility) {
            case 'public' :
                UnitTesting::assertTrue($reflectedClass->getProperty($propertyName)->isPublic(), $customFailMessage);
                break;
            case 'private':
                UnitTesting::assertTrue($reflectedClass->getProperty($propertyName)->isPrivate(), $customFailMessage);
                break;
            case 'protected':
                UnitTesting::assertTrue($reflectedClass->getProperty($propertyName)->isProtected(), $customFailMessage);
                break;
            default:
                UnitTesting::fail('Object property "' . $propertyName . '" accessibility is unconfirmed.');
        }

        // Test property type
        $type = $reflectedClass->getProperty($propertyName)->getType();
        if (is_null($type)) {
            $actualType = 'unset';
        } else {
            $actualType = $type->getName();
        }
        if ($actualType === 'unset' && $propertyValueType === 'mixed') {
            $actualType = 'mixed';
        }
        UnitTesting::assertSame($propertyValueType, $actualType,
            'Object property "' . $propertyName . '" type isn\'t ' . $propertyValueType . '.');


        if (is_null($positionInConstructor) || $reflectedClass->getProperty($propertyName)->isStatic()) {

            // Test for inconsistencies in test configuration
            if (!is_null($positionInConstructor)) {
                UnitTesting::fail('Object property "' . $propertyName .
                    '" has an invalid positionConstructorArray. Static properties can\'t be on constructor.');
            }

            //Test property default value
            if (version_compare(PHP_VERSION, '8.0.0') >= 0) {

                // Simple way in PHP 8.0.0 or higher
                if ($reflectedClass->getProperty($propertyName)->hasDefaultValue()) {
                    $actualDefault = $reflectedClass->getProperty($propertyName)->getDefaultValue();
                } else {
                    $actualDefault = "unset";
                }

            } else {

                // For previous PHP versions use this longer way
                if ($reflectedClass->getProperty($propertyName)->isStatic()) {
                    $actualDefault = $reflectedProp->getValue();
                } else {
                    if ($reflectedProp->isInitialized($reflectedClass->newInstanceWithoutConstructor())) {
                        $actualDefault = $reflectedProp->getValue($reflectedClass->newInstanceWithoutConstructor());
                    } else {
                        $actualDefault = 'unset';
                    }
                }
            }
            UnitTesting::assertSame($propertyDefaultValue, $actualDefault,
                'Object property "' . $propertyName . '" default isn\'t ' . $propertyDefaultValue . '.');

        } else {

            // Test property constructor parameter
            UnitTesting::assertTrue($propertyDefaultValue === 'unset', 'Object property "' . $propertyName .
                '" can\'t have a default, It is initialized by the constructor.');

            $expectedValue = '2.2';
            if ($propertyValueType !== 'unset' && $propertyValueType !== 'mixed') {
                try {
                    settype($expectedValue, $propertyValueType);
                } catch (Error $e) {
                    UnitTesting::fail('We were not able to calculate expected value for object property "' . $propertyName .
                        '" with type ' . $propertyValueType . '.');
                }
            }

            // Create array of constructors params
            $objectConstructor = $reflectedClass->getMethod('__construct');
            $constructorParams = $objectConstructor->getParameters();
            $objectConstructorParams = [];
            foreach ($constructorParams as $param) {
                $paramValue = '0';
                $paramType = $param->getType();
                if (!is_null($paramType)) {
                    settype($paramValue, $paramType->getName());
                }
                $objectConstructorParams[] = $paramValue;
            }

            try {
                $objectConstructorParams[$positionInConstructor - 1] = $expectedValue;
            } catch (Error $e) {
                UnitTesting::fail('Object property "' . $propertyName .
                    '" parameter "positionInConstructor" is wrong, position should be from 1 to n parameters. Please review your test configuration.');
            }

            try {
                $newObject = $reflectedClass->newInstanceArgs($objectConstructorParams);
            } catch (Error $e) {
                UnitTesting::fail('There is a mismatch between the constructor parameters types and the class properties types.');
            }

            $actualValue = $reflectedProp->getValue($newObject);

            UnitTesting::assertSame($expectedValue, $actualValue,
                'Object property "' . $propertyName . '" positionConstructorArray isn\'t ' . $positionInConstructor . '.');

        }

    }
}