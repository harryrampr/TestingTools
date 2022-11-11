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
     * Test a class, interface or trait.
     *
     * @param string $classFullName The class name including namespace.
     * @param string $classNameSpace The class namespace.
     * @param array $classesExtendIt List of parent classes and traits that extend this class.
     * @param array $interfacesImplements List of interfaces that are implemented in this class.
     * @param bool $hasConstructor State if the class has constructor, defaults to true.
     * @param bool $classIsFinal State if the class is final, defaults to false.
     * @param bool $classIsInstantiable State if the class is Instantiable, defaults to true.
     * @param bool $classIsAbstract State if the class is static, defaults to false.
     * @param bool $isInterface State if the class is an interface, defaults to false.
     * @param bool $isTrait State if the class is a trait, defaults to false.
     * @return void
     * @throws ReflectionException
     */
    public static function testClass(string $classFullName,
                                     string $classNameSpace = '',
                                     array  $classesExtendIt = [],
                                     array  $interfacesImplements = [],
                                     bool   $hasConstructor = true,
                                     bool   $classIsFinal = false,
                                     bool   $classIsInstantiable = true,
                                     bool   $classIsAbstract = false,
                                     bool   $isInterface = false,
                                     bool   $isTrait = false

    ): void
    {
        $classShortName = basename($classFullName);

        // Test class exist
        self::testClassTraitOrInterfaceExist($classFullName);

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);

        // Test class namespace
        echo "-Test that class \"{$classShortName}\" has namespace: {$classNameSpace}." . PHP_EOL;
        TestCase::assertSame($classNameSpace, $reflectedClass->getNamespaceName(),
            sprintf('Class "%s" namespace isn\'t %s.', $classShortName, $classNameSpace));


        // To do: Add more tests here.

        // Test if class has a constructor
        echo sprintf('-Test that class "%s" %s a constructor.', $classShortName,
                ($hasConstructor ? "has" : "hasn't")) . PHP_EOL;
        TestCase::assertSame($hasConstructor, !is_null($reflectedClass->getConstructor()),
            sprintf('Class "%s" %s a constructor.', $classShortName,
                (!$hasConstructor ? "has" : "hasn't")));

        // Test if Class is final.
        echo sprintf('-Test that class "%s" %s final.', $classShortName,
                ($classIsFinal ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($classIsFinal, $reflectedClass->isFinal(),
            sprintf('Class "%s" %s final.', $classShortName, (!$classIsFinal ? "is" : "isn't")));

        // Test if Class is instantiable.
        echo sprintf('-Test that class "%s" %s instantiable.', $classShortName,
                ($classIsInstantiable ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($classIsInstantiable, $reflectedClass->isInstantiable(),
            sprintf('Class "%s" %s instantiable.', $classShortName, (!$classIsInstantiable ? "is" : "isn't")));

        // Test if Class is abstract.
        echo sprintf('-Test that class "%s" %s abstract.', $classShortName,
                ($classIsAbstract ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($classIsAbstract, $reflectedClass->isAbstract(),
            sprintf('Class "%s" %s abstract.', $classShortName, (!$classIsAbstract ? "is" : "isn't")));

        // Test if is interface.
        echo sprintf('-Test that class "%s" %s an interface.', $classShortName,
                ($isInterface ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isInterface, $reflectedClass->isInterface(),
            sprintf('Class "%s" %s an interface.', $classShortName, (!$isInterface ? "is" : "isn't")));

        // Test if is trait.
        echo sprintf('-Test that class "%s" %s a trait.', $classShortName,
                ($isTrait ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isTrait, $reflectedClass->isTrait(),
            sprintf('Class "%s" %s a trait.', $classShortName, (!$isTrait ? "is" : "isn't")));

    }

    /**
     * Test that a class, interface or trait exist.
     *
     * @param string $fullName The name including namespace.
     * @return void
     */
    static protected function testClassTraitOrInterfaceExist(string $fullName): void
    {
        echo PHP_EOL;

        $classExits = class_exists($fullName);
        $interfaceExits = interface_exists($fullName);
        $traitExits = trait_exists($fullName);

        $kindTested = $interfaceExits ? 'interface' : ($traitExits ? 'trait' : 'class');

        // Test class, interface or trait exist
        echo sprintf('-Test that %s "%s" exist.', $kindTested, basename($fullName)) . PHP_EOL;
        TestCase::assertTrue($classExits || $interfaceExits || $traitExits,
            sprintf('Class "%s" wasn\'t found.', $fullName));

    }

    /**
     * Test a class property for the following attributes:
     *
     * @param string $classFullName The property's class name including namespace.
     * @param string $propertyName The name of the property to test.
     * @param string $propertyAccessMode The kind of property's access mode, default is public.
     * @param string $propertyValueType The property's value type, default is 'unset' for mixed.
     * @param string $propertyDefaultValue The property's default value, default is 'unset' for no value.
     * @param int|null $positionInConstructor The property's position in __constructor, default is null for no position.
     * @param bool $propertyIsStatic State if the property is static, default is false.
     *
     * @return void
     * @throws ReflectionException
     */
    public static function testClassProperty(string $classFullName,
                                             string $propertyName,
                                             string $propertyAccessMode = 'public',
                                             string $propertyValueType = 'unset',
                                                    $propertyDefaultValue = 'unset',
                                             ?int   $positionInConstructor = null,
                                             bool   $propertyIsStatic = false): void
    {
        $classShortName = basename($classFullName);

        // Test class exist
        self::testClassTraitOrInterfaceExist($classFullName);

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($propertyAccessMode != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in class
        echo "-Test that property \"{$propertyName}\" exist." . PHP_EOL;
        TestCase::assertTrue($reflectedClass->hasProperty($propertyName),
            'Class property "' . $propertyName . '" isn\'t available in class.');

        // Test is property is static
        echo "-Test that property \"{$propertyName}\" " . ($propertyIsStatic ? "is" : "isn't") . " static." . PHP_EOL;
        TestCase::assertSame($propertyIsStatic, $reflectedProp->isStatic(),
            'property "' . $propertyName . '" ' . (!$propertyIsStatic ? "is set as" : "isn't") . ' static.');

        // Test property accessibility
        echo "-Test that property \"{$propertyName}\" is {$propertyAccessMode}." . PHP_EOL;
        $customFailMessage = 'Class property "' . $propertyName .
            '" accessibility isn\'t ' . $propertyAccessMode . '.';
        switch ($propertyAccessMode) {
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
        echo "-Test that property \"{$propertyName}\" is type {$propertyValueType}." . PHP_EOL;
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
            echo "-Test that property \"{$propertyName}\" default is {$propertyDefaultValue}." . PHP_EOL;
            if (version_compare(PHP_VERSION, '8.0.0') >= 0) {

                // Simple way in PHP 8.0.0 or higher
                /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
                if ($reflectedProp->hasDefaultValue()) {
                    /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
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
            echo "-Test that property \"{$propertyName}\" is in position #{$positionInConstructor} in constructor." . PHP_EOL;
            // Test for inconsistencies
            TestCase::assertTrue($propertyDefaultValue === 'unset', 'Class property "' .
                $propertyName . '" shouldn\'t have a default value if it\'s initialized by the constructor.');

            TestCase::assertGreaterThanOrEqual(1, $positionInConstructor, 'Class property "' .
                $propertyName . '" parameter "positionInConstructor" is wrong, ' .
                'position should be 1 or greater. Please review your test configuration.');

            // Test property position in constructor
            $expectedValue = '2.2';
            if ($propertyValueType !== 'unset' && $propertyValueType !== 'mixed') {
                try {
                    settype($expectedValue, $propertyValueType);
                } catch (Error $e) {
                    TestCase::fail('We were not able to calculate expected value for class property "' .
                        $propertyName . '" with type ' . $propertyValueType . '.');
                }
            }

            // Test if constructor exist
            $classConstructor = $reflectedClass->getConstructor();
            TestCase::assertFalse(is_null($classConstructor),
                sprintf('There is no constructor for class property "%s".', $propertyName));

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
                    '" parameter "positionInConstructor" is wrong, ' .
                    'position should be from 1 to n parameters. Please review your test configuration.');
            }

            try {
                $newObject = $reflectedClass->newInstanceArgs($classConstructorParams);
            } catch (Error $e) {
                TestCase::fail('There is a mismatch between the constructor parameters ' .
                    'types and the class properties types.');
            }

            if ($reflectedProp->isStatic()) {
                $actualValue = $reflectedProp->getValue();
            } else {
                $actualValue = $reflectedProp->getValue($newObject);
            }

            TestCase::assertSame($expectedValue, $actualValue,
                'Class property "' . $propertyName . '" position in constructor isn\'t ' .
                $positionInConstructor . '.');
        }
    }

}