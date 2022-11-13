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
     * Test a class, interface or trait using PHPUnit.
     *
     * This test performs over 12 asserts by just adding one line to your code.
     * It generates output that let you know the assertions fulfilled and the
     * problems found.
     *
     * @param string $structureFullName The structure name including namespace.
     * @param string $nameSpace The structure namespace, default is an empty string.
     * @param string $parentClass Name of parent class extended by this structure, default is an empty string.
     * @param array $implementedInterfaces List of interfaces implemented by this structure, default is an empty array.
     * @param array $usedTraits List of traits used by this structure, default is an empty array.
     * @param bool $hasConstructor State if the structure has constructor, default is true.
     * @param bool $isFinal State if the structure is final, default is false.
     * @param bool $isInstantiable State if the structure is Instantiable, default is true.
     * @param bool $isAbstract State if the structure is static, default is false.
     * @param bool $isInterface State if the structure is an interface, default is false.
     * @param bool $isTrait State if the structure is a trait, default is false.
     * @return void
     * @throws ReflectionException
     */
    public function utility_test_class_structure(string $structureFullName,
                                                 string $nameSpace = '',
                                                 string $parentClass = '',
                                                 array  $implementedInterfaces = [],
                                                 array  $usedTraits = [],
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
        $structureType = $this->exists_class_interface_or_trait($structureFullName);

        // Test class, interface or trait exist
        echo PHP_EOL;
        echo sprintf('-Testing that structure "%s" exist.%s', $structureShortName, PHP_EOL);
        TestCase::assertTrue($structureType !== '',
            sprintf("No class, interface or trait named \"%s\" wasn't found.%s",
                $structureFullName, PHP_EOL));

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($structureFullName);

        // Test structure namespace
        echo sprintf('-Testing that structure "%s" has namespace: %s.%s', $structureShortName,
            $nameSpace, PHP_EOL);
        TestCase::assertSame($nameSpace, $reflectedClass->getNamespaceName(),
            sprintf('Structure "%s" namespace isn\'t %s.%s', $structureShortName,
                $nameSpace, PHP_EOL));


        // Todo: Add more tests here.

        // Test if structure has a constructor
        echo sprintf('-Testing that structure "%s" %s a constructor.%s', $structureShortName,
            ($hasConstructor ? "has" : "hasn't"), PHP_EOL);
        TestCase::assertSame($hasConstructor, !is_null($reflectedClass->getConstructor()),
            sprintf('Structure "%s" %s a constructor.%s', $structureShortName,
                (!$hasConstructor ? "has" : "hasn't"), PHP_EOL));

        // Test if structure is final.
        echo sprintf('-Testing that structure "%s" %s final.%s', $structureShortName,
            ($isFinal ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isFinal, $reflectedClass->isFinal(),
            sprintf('Structure "%s" %s final.%s', $structureShortName,
                (!$isFinal ? "is" : "isn't"), PHP_EOL));

        // Test if Structure is instantiable.
        echo sprintf('-Testing that structure "%s" %s instantiable.%s', $structureShortName,
            ($isInstantiable ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isInstantiable, $reflectedClass->isInstantiable(),
            sprintf('Structure "%s" %s instantiable.%s', $structureShortName,
                (!$isInstantiable ? "is" : "isn't"), PHP_EOL));

        // Test if Structure is abstract.
        echo sprintf('-Testing that structure "%s" %s abstract.%s', $structureShortName,
            ($isAbstract ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isAbstract, $reflectedClass->isAbstract(),
            sprintf('Structure "%s" %s abstract.%s', $structureShortName,
                (!$isAbstract ? "is" : "isn't"), PHP_EOL));

        // Test if is interface.
        echo sprintf('-Testing that structure "%s" %s an interface.%s', $structureShortName,
            ($isInterface ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isInterface, $reflectedClass->isInterface(),
            sprintf('Structure "%s" %s an interface.%s', $structureShortName,
                (!$isInterface ? "is" : "isn't"), PHP_EOL));

        // Test if is trait.
        echo sprintf('-Testing that structure "%s" %s a trait.%s', $structureShortName,
            ($isTrait ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isTrait, $reflectedClass->isTrait(),
            sprintf('Structure "%s" %s a trait.%s', $structureShortName,
                (!$isTrait ? "is" : "isn't"), PHP_EOL));

        // Test if is class.
        $isClass = !$isInterface && !$isTrait;
        echo sprintf('-Testing that structure "%s" %s a class.%s', $structureShortName,
            ($isClass ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isClass, $structureType === 'class',
            sprintf('Structure "%s" %s a class.%s', $structureShortName,
                (!$isClass ? "is" : "isn't"), PHP_EOL));

        // Test structures extend it list.
        $actualParentClass = $reflectedClass->getParentClass();
        $actualParentClassName = $actualParentClass ? $actualParentClass->getShortName() : 'no parent class';
        $parentClass = $parentClass == '' ? 'no parent class' : $parentClass;
        echo sprintf('-Testing that structure "%s" extends: %s.%s', $structureShortName,
            $parentClass, PHP_EOL);
        TestCase::assertSame($parentClass, $actualParentClassName,
            sprintf('Structure "%s" doesn\'t extend: %s.%s', $structureShortName,
                $parentClass, PHP_EOL));

        // Test list of implemented interfaces.
        $actualImplementedInterfaces = $reflectedClass->getInterfaceNames();
        $unaccountedInterfaces = $actualImplementedInterfaces;
        foreach ($implementedInterfaces as $expectedImplemented) {

            echo sprintf('-Testing that structure "%s" implements: %s.%s', $structureShortName,
                $expectedImplemented, PHP_EOL);

            // Look for expected interface in actual implemented interfaces list
            $interfaceFound = false;
            foreach ($actualImplementedInterfaces as $actualImplemented) {

                if ($actualImplemented === $expectedImplemented ||
                    basename($actualImplemented) === $expectedImplemented) {

                    // Interface was found
                    $interfaceFound = true;
                    // Remove item found from unaccounted interfaces
                    $key = array_search($actualImplemented, $unaccountedInterfaces);
                    if ($key) unset($unaccountedInterfaces[$key]);
                    break;

                }
                // interface was not found
            }

            TestCase::assertTrue($interfaceFound,
                sprintf('Not able to validate that structure "%s" implements: %s.%s%s %s%s',
                    $structureShortName, $expectedImplemented, PHP_EOL,
                    'These are the ones found:', json_encode($actualImplementedInterfaces), PHP_EOL));
        }

        // Test for unaccounted interfaces
        echo sprintf('-Testing that structure "%s" only implements the %d specified interfaces.%s',
            $structureShortName, count($implementedInterfaces), PHP_EOL);
        TestCase::assertSame(count($implementedInterfaces), count($actualImplementedInterfaces),
            sprintf('The following interfaces were implemented, but not specified by the test parameters:%s%s%s',
                PHP_EOL, json_encode($unaccountedInterfaces), PHP_EOL));


        // Test list of used traits.
        $actualUsedTraits = $reflectedClass->getTraitNames();
        $unaccountedTraits = $actualUsedTraits;
        foreach ($usedTraits as $expectedUsed) {

            echo sprintf('-Testing that structure "%s" uses: %s.%s', $structureShortName,
                $expectedUsed, PHP_EOL);

            // Look for expected trait in actual used traits list
            $traitFound = false;
            foreach ($actualUsedTraits as $actualUsed) {

                if ($actualUsed === $expectedUsed ||
                    basename($actualUsed) === $expectedUsed) {

                    // Trait was found
                    $traitFound = true;
                    // Remove item found from unaccounted traits
                    $key = array_search($actualUsed, $unaccountedTraits);
                    if ($key) unset($unaccountedTraits[$key]);
                    break;

                }
                // trait was not found
            }

            TestCase::assertTrue($traitFound,
                sprintf('Not able to validate that structure "%s" uses: %s.%s%s %s%s',
                    $structureShortName, $expectedUsed, PHP_EOL,
                    'These are the ones found:', json_encode($actualUsedTraits), PHP_EOL));
        }

        // Test for unaccounted traits
        echo sprintf('-Testing that structure "%s" only uses the %d specified traits.%s',
            $structureShortName, count($usedTraits), PHP_EOL);
        TestCase::assertSame(count($usedTraits), count($actualUsedTraits),
            sprintf('The following traits were used, but not specified by the test parameters:%s%s%s',
                PHP_EOL, json_encode($unaccountedTraits), PHP_EOL));

    }

    /**
     * Find if a structure exists, either class, interface or trait.
     *
     * @param string $fullName The name including namespace.
     * @return string The type of structure found, an empty string if nothing found.
     */
    private function exists_class_interface_or_trait(string $fullName): string
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
     * @param string $parentFullName The property's parent structure name including namespace.
     * @param string $propertyName The name of the property to test.
     * @param string $accessMode The kind of property's access mode, default is public.
     * @param string $valueType The property's value type, default is 'unset' for mixed type.
     * @param string $defaultValue The property's default value, default is 'unset' for no value.
     * @param int|null $positionInConstructor The property's position in __constructor, default is null for no position.
     * @param bool $isStatic State if the property is static, default is false.
     *
     * @return void
     * @throws ReflectionException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function utility_test_class_property(string $parentFullName,
                                                string $propertyName,
                                                string $accessMode = 'public',
                                                string $valueType = 'unset',
                                                       $defaultValue = 'unset',
                                                ?int   $positionInConstructor = null,
                                                bool   $isStatic = false): void
    {

        // Find if file exist and what kind of structure was found
        $structureType = $this->exists_class_interface_or_trait($parentFullName);

        // Test class, interface or trait exist
        echo PHP_EOL;

        echo sprintf('-Testing that parent structure "%s" exist.%s', basename($parentFullName), PHP_EOL);
        TestCase::assertTrue($structureType !== '',
            sprintf("Parent structure \"%s\" wasn't found.%s", $parentFullName, PHP_EOL));

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($parentFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($accessMode != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in structure
        echo sprintf('-Testing that property "%s" exist.%s', $propertyName, PHP_EOL);
        TestCase::assertTrue($reflectedClass->hasProperty($propertyName),
            sprintf('Property "%s" isn\'t available in structure.%s', $propertyName, PHP_EOL));

        // Test is property is static
        echo sprintf('-Testing that property "%s" %s static.%s',
            $propertyName, ($isStatic ? "is" : "isn't"), PHP_EOL);
        TestCase::assertSame($isStatic, $reflectedProp->isStatic(),
            sprintf('Structure property "%s" %s static.%s', $propertyName,
                (!$isStatic ? "is set as" : "isn't"), PHP_EOL));

        // Test property accessibility
        echo sprintf('-Testing that property "%s" is %s.%s', $propertyName, $accessMode, PHP_EOL);
        $customFailMessage = sprintf('Structure property "%s" accessibility isn\'t %s.%s',
            $propertyName, $accessMode, PHP_EOL);
        switch ($accessMode) {
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
        echo sprintf('-Testing that property "%s" is type %s.%s',
            $propertyName, $valueType, PHP_EOL);
        if ($reflectedProp->hasType()) {
            $actualType = $reflectedProp->getType()->getName();
        } else {
            $actualType = 'unset';
        }
        if ($actualType === 'unset' && $valueType === 'mixed') {
            $actualType = 'mixed';
        }
        TestCase::assertSame($valueType, $actualType,
            sprintf('Structure property "%s" type isn\'t %s.%s', $propertyName,
                $valueType, PHP_EOL));

        if (is_null($positionInConstructor)) {

            // Get the printable value of default value
            if (is_scalar($defaultValue)) {
                $defaultValuePrintable = $defaultValue;
            } else {
                if (is_null($defaultValue)) {
                    $defaultValuePrintable = "set to null";
                } elseif (is_array($defaultValue)) {
                    $defaultValuePrintable = "an array";
                } elseif (is_object($defaultValue)) {
                    $defaultValuePrintable = "an object";
                } elseif (is_callable($defaultValue)) {
                    $defaultValuePrintable = "a callable";
                } else {
                    $defaultValuePrintable = "an item";
                }
            }

            //Test property default value
            echo sprintf('-Testing that property "%s" default is %s.%s', $propertyName,
                $defaultValuePrintable, PHP_EOL);
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
                        if (is_null($actualDefault) && $defaultValue === 'unset') {
                            $actualDefault = 'unset';
                        }
                    } else {
                        $actualDefault = 'unset';
                    }
                }
            }
            TestCase::assertSame($defaultValue, $actualDefault,
                sprintf('Structure property "%s" default isn\'t %s.%s',
                    $propertyName, $defaultValuePrintable, PHP_EOL));

        } else {
            echo sprintf('-Testing that property "%s" is in position #%d in constructor.%s',
                $propertyName, $positionInConstructor, PHP_EOL);
            // Test for inconsistencies
            TestCase::assertTrue($defaultValue === 'unset',
                sprintf('Structure property "%s" shouldn\'t have %s.%s',
                    $propertyName, 'a default value if it\'s initialized by the constructor', PHP_EOL));

            TestCase::assertGreaterThanOrEqual(1, $positionInConstructor,
                sprintf('Structure property "%s" parameter "positionInConstructor" is wrong, %s. %s.%s',
                    $propertyName, 'position should be 1 or greater',
                    'Please review your test configuration', PHP_EOL));

            // Test property position in constructor
            $expectedValue = '2.2';
            if ($valueType !== 'unset' && $valueType !== 'mixed') {
                try {
                    settype($expectedValue, $valueType);
                } catch (Error $e) {
                    TestCase::fail(sprintf('We were not able to %s "%s" with type %s.',
                        'calculate expected value for structure property', $propertyName, $valueType));
                }
            }

            // Test if constructor exist
            $structureConstructor = $reflectedClass->getConstructor();
            TestCase::assertFalse(is_null($structureConstructor),
                sprintf('There is no constructor for structure property "%s".%s',
                    $propertyName, PHP_EOL));

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

                TestCase::assertTrue($positionInConstructor > 0 &&
                    $positionInConstructor <= count($classConstructorParams),
                    sprintf('Structure property "%s" parameter "positionInConstructor" is wrong, %s. %s.%s',
                        $propertyName, 'position should be from 1 to n parameters',
                        'Use "null" to state that property is not in the constructor', PHP_EOL));

                $classConstructorParams[$positionInConstructor - 1] = $expectedValue;

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
                    sprintf('Structure property "%s" position in constructor isn\'t %d.%s',
                        $propertyName, $positionInConstructor, PHP_EOL));

            } else {
                TestCase::markTestSkipped(sprintf('The structure "%s" isn\'t instantiable, we are not %s.%s',
                    basename($parentFullName), 'able to test the property\'s position in constructor', PHP_EOL));
            }
        }
    }

}