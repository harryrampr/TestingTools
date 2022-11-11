<?php

namespace TestingTools\UnitTesting;

use Error;
use PHPUnit\Framework\TestCase as TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Utilities that simplify PHPUnit testing of classes, interfaces and traits.
 * @author Harry Ramirez
 */
trait ClassUtilities
{

    /**
     * Test a class, interface or trait.
     *
     * @param string $structureFullName The structure name including namespace.
     * @param string $nameSpace The structure namespace.
     * @param array $structuresExtendIt List of parent classes and traits that extend this structure.
     * @param array $interfacesImplements List of interfaces that are implemented in this structure.
     * @param bool $hasConstructor State if the structure has constructor, defaults to true.
     * @param bool $isFinal State if the structure is final, defaults to false.
     * @param bool $isInstantiable State if the structure is Instantiable, defaults to true.
     * @param bool $isAbstract State if the structure is static, defaults to false.
     * @param bool $isInterface State if the structure is an interface, defaults to false.
     * @param bool $isTrait State if the structure is a trait, defaults to false.
     * @return void
     * @throws ReflectionException
     */
    public function utilityToTestClassTraitOrInterface(string $structureFullName,
                                                       string $nameSpace = '',
                                                       array  $structuresExtendIt = [],
                                                       array  $interfacesImplements = [],
                                                       bool   $hasConstructor = true,
                                                       bool   $isFinal = false,
                                                       bool   $isInstantiable = true,
                                                       bool   $isAbstract = false,
                                                       bool   $isInterface = false,
                                                       bool   $isTrait = false

    ): void
    {
        $structureShortName = basename($structureFullName);

        // Find if file exist and what kind of structure was found
        $structureType = $this->utilityExistsClassTraitOrInterface($structureFullName);

        // Test class, interface or trait exist
        echo PHP_EOL;
        echo sprintf('-Testing that structure "%s" exist.', $structureShortName) . PHP_EOL;
        TestCase::assertTrue($structureType !== '',
            sprintf("No class, interface or trait named \"%s\" wasn't found.", $structureFullName));

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($structureFullName);

        // Test structure namespace
        echo sprintf('-Testing that structure "%s" has namespace: %s.', $structureShortName, $nameSpace) . PHP_EOL;
        TestCase::assertSame($nameSpace, $reflectedClass->getNamespaceName(),
            sprintf('Structure "%s" namespace isn\'t %s.', $structureShortName, $nameSpace));


        // Todo: Add more tests here.

        // Test if structure has a constructor
        echo sprintf('-Testing that structure "%s" %s a constructor.', $structureShortName,
                ($hasConstructor ? "has" : "hasn't")) . PHP_EOL;
        TestCase::assertSame($hasConstructor, !is_null($reflectedClass->getConstructor()),
            sprintf('Structure "%s" %s a constructor.', $structureShortName,
                (!$hasConstructor ? "has" : "hasn't")));

        // Test if structure is final.
        echo sprintf('-Testing that structure "%s" %s final.', $structureShortName,
                ($isFinal ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isFinal, $reflectedClass->isFinal(),
            sprintf('Structure "%s" %s final.', $structureShortName, (!$isFinal ? "is" : "isn't")));

        // Test if Structure is instantiable.
        echo sprintf('-Testing that structure "%s" %s instantiable.', $structureShortName,
                ($isInstantiable ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isInstantiable, $reflectedClass->isInstantiable(),
            sprintf('Structure "%s" %s instantiable.', $structureShortName, (!$isInstantiable ? "is" : "isn't")));

        // Test if Structure is abstract.
        echo sprintf('-Testing that structure "%s" %s abstract.', $structureShortName,
                ($isAbstract ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isAbstract, $reflectedClass->isAbstract(),
            sprintf('Structure "%s" %s abstract.', $structureShortName, (!$isAbstract ? "is" : "isn't")));

        // Test if is interface.
        echo sprintf('-Testing that structure "%s" %s an interface.', $structureShortName,
                ($isInterface ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isInterface, $reflectedClass->isInterface(),
            sprintf('Structure "%s" %s an interface.', $structureShortName, (!$isInterface ? "is" : "isn't")));

        // Test if is trait.
        echo sprintf('-Testing that structure "%s" %s a trait.', $structureShortName,
                ($isTrait ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isTrait, $reflectedClass->isTrait(),
            sprintf('Structure "%s" %s a trait.', $structureShortName, (!$isTrait ? "is" : "isn't")));

        // Test if is class.
        $isClass = !$isInterface && !$isTrait;
        echo sprintf('-Testing that structure "%s" %s a class.', $structureShortName,
                ($isClass ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($isClass, $structureType === 'class',
            sprintf('Structure "%s" %s a class.', $structureShortName, (!$isClass ? "is" : "isn't")));

    }

    /**
     * Find if a structure exists, either class, interface or trait.
     *
     * @param string $fullName The name including namespace.
     * @return string The type of structure found, an empty string if nothing found.
     */
    private function utilityExistsClassTraitOrInterface(string $fullName): string
    {

        if (class_exists($fullName)) return 'class';
        if (interface_exists($fullName)) return 'interface';
        if (trait_exists($fullName)) return 'trait';

        // Not found
        return '';
    }

    /**
     * Test a structure property for the following attributes:
     *
     * @param string $parentStructureFullName The property's parent structure name including namespace.
     * @param string $propertyName The name of the property to test.
     * @param string $propertyAccessMode The kind of property's access mode, default is public.
     * @param string $propertyValueType The property's value type, default is 'unset' for mixed.
     * @param string $propertyDefaultValue The property's default value, default is 'unset' for no value.
     * @param int|null $positionInConstructor The property's position in __constructor, default is null for no position.
     * @param bool $propertyIsStatic State if the property is static, default is false.
     *
     * @return void
     * @throws ReflectionException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function utilityToTestClassProperty(string $parentStructureFullName,
                                               string $propertyName,
                                               string $propertyAccessMode = 'public',
                                               string $propertyValueType = 'unset',
                                                      $propertyDefaultValue = 'unset',
                                               ?int   $positionInConstructor = null,
                                               bool   $propertyIsStatic = false): void
    {

        // Find if file exist and what kind of structure was found
        $structureType = $this->utilityExistsClassTraitOrInterface($parentStructureFullName);

        // Test class, interface or trait exist
        echo PHP_EOL;

        echo sprintf('-Testing that parent structure "%s" exist.', basename($parentStructureFullName)) . PHP_EOL;
        TestCase::assertTrue($structureType !== '',
            sprintf("Parent structure \"%s\" wasn't found.", $parentStructureFullName));

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($parentStructureFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($propertyAccessMode != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in structure
        echo sprintf('-Testing that property "%s" exist.', $propertyName) . PHP_EOL;
        TestCase::assertTrue($reflectedClass->hasProperty($propertyName),
            sprintf('Property "%s" isn\'t available in structure.', $propertyName));

        // Test is property is static
        echo sprintf('-Testing that property "%s" %s static.',
                $propertyName, ($propertyIsStatic ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($propertyIsStatic, $reflectedProp->isStatic(),
            sprintf('Structure property "%s" %s static.', $propertyName,
                (!$propertyIsStatic ? "is set as" : "isn't")));

        // Test property accessibility
        echo sprintf('-Testing that property "%s" is %s.', $propertyName, $propertyAccessMode) . PHP_EOL;
        $customFailMessage = sprintf('Structure property "%s" accessibility isn\'t %s.',
            $propertyName, $propertyAccessMode);
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
                TestCase::fail(sprintf('Structure property "%s" accessibility is unconfirmed.', $propertyName));
        }

        // Test property type
        echo sprintf('-Testing that property "%s" is type %s.', $propertyName, $propertyValueType) . PHP_EOL;
        if ($reflectedProp->hasType()) {
            $actualType = $reflectedProp->getType()->getName();
        } else {
            $actualType = 'unset';
        }
        if ($actualType === 'unset' && $propertyValueType === 'mixed') {
            $actualType = 'mixed';
        }
        TestCase::assertSame($propertyValueType, $actualType,
            sprintf('Structure property "%s" type isn\'t %s.', $propertyName, $propertyValueType));

        if (is_null($positionInConstructor)) {

            // Get the printable value of default value
            if (is_scalar($propertyDefaultValue)) {
                $defaultValuePrintable = $propertyDefaultValue;
            } else {
                if (is_null($propertyDefaultValue)) {
                    $defaultValuePrintable = "set to null";
                } elseif (is_array($propertyDefaultValue)) {
                    $defaultValuePrintable = "an array";
                } elseif (is_object($propertyDefaultValue)) {
                    $defaultValuePrintable = "an object";
                } elseif (is_callable($propertyDefaultValue)) {
                    $defaultValuePrintable = "a callable";
                } else {
                    $defaultValuePrintable = "an item";
                }
            }

            //Test property default value
            echo sprintf('-Testing that property "%s" default is %s.', $propertyName,
                    $defaultValuePrintable) . PHP_EOL;
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
                sprintf('Structure property "%s" default isn\'t %s.', $propertyName, $defaultValuePrintable));

        } else {
            echo sprintf('-Testing that property "%s" is in position #%d in constructor.',
                    $propertyName, $positionInConstructor) . PHP_EOL;
            // Test for inconsistencies
            TestCase::assertTrue($propertyDefaultValue === 'unset',
                sprintf('Structure property "%s" shouldn\'t have %s',
                    $propertyName, 'a default value if it\'s initialized by the constructor.'));

            TestCase::assertGreaterThanOrEqual(1, $positionInConstructor,
                sprintf('Structure property "%s" parameter "positionInConstructor" is wrong, %s %s',
                    $propertyName, 'position should be 1 or greater.',
                    'Please review your test configuration.'));

            // Test property position in constructor
            $expectedValue = '2.2';
            if ($propertyValueType !== 'unset' && $propertyValueType !== 'mixed') {
                try {
                    settype($expectedValue, $propertyValueType);
                } catch (Error $e) {
                    TestCase::fail(sprintf('We were not able to %s "%s" with type %s.',
                        'calculate expected value for structure property', $propertyName, $propertyValueType));
                }
            }

            // Test if constructor exist
            $structureConstructor = $reflectedClass->getConstructor();
            TestCase::assertFalse(is_null($structureConstructor),
                sprintf('There is no constructor for structure property "%s".', $propertyName));

            if ($reflectedClass->isInstantiable()) {
                // Generate array of constructors params
                $constructorParams = $structureConstructor->getParameters();
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
                    TestCase::fail(sprintf('Structure property "%s" parameter "positionInConstructor" is wrong, %s %s',
                        $propertyName, 'position should be from 1 to n parameters.',
                        'Please review your test configuration.'));
                }

                try {
                    $newObject = $reflectedClass->newInstanceArgs($classConstructorParams);
                } catch (Error $e) {
                    TestCase::fail('There is a mismatch between the constructor parameters ' .
                        'types and the structure properties types.');
                }

                if ($reflectedProp->isStatic()) {
                    $actualValue = $reflectedProp->getValue();
                } else {
                    $actualValue = $reflectedProp->getValue($newObject);
                }

                TestCase::assertSame($expectedValue, $actualValue,
                    sprintf('Structure property "%s" position in constructor isn\'t %d.',
                        $propertyName, $positionInConstructor));

            } else {
                TestCase::markTestSkipped(sprintf('The structure "%s" isn\'t instantiable, we are not %s.',
                    basename($parentStructureFullName), 'able to test the property\'s position in constructor'));
            }
        }
    }

}