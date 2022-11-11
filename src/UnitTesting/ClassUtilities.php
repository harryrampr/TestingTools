<?php

namespace Hrpdevtools\TestingTools\UnitTesting;

use Error;
use PHPUnit\Framework\TestCase as TestCase;
use ReflectionClass;
use ReflectionException;

trait ClassUtilities
{

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
     * @noinspection PhpMissingParamTypeInspection
     */
    public function utilityTestClassProperty(string $classFullName,
                                             string $propertyName,
                                             string $propertyAccessMode = 'public',
                                             string $propertyValueType = 'unset',
                                                    $propertyDefaultValue = 'unset',
                                             ?int   $positionInConstructor = null,
                                             bool   $propertyIsStatic = false): void
    {

        // Test class exist
        $this->utilityExistsClassTraitOrInterface($classFullName);

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);
        $reflectedProp = $reflectedClass->getProperty($propertyName);
        if ($propertyAccessMode != 'public') {
            $reflectedProp->setAccessible(true);
        }

        // Test property exists in class
        echo sprintf('-Test that property "%s" exist.', $propertyName) . PHP_EOL;
        TestCase::assertTrue($reflectedClass->hasProperty($propertyName),
            sprintf('Class property "%s" isn\'t available in class.', $propertyName));

        // Test is property is static
        echo sprintf('-Test that property "%s" %s static.',
                $propertyName, ($propertyIsStatic ? "is" : "isn't")) . PHP_EOL;
        TestCase::assertSame($propertyIsStatic, $reflectedProp->isStatic(),
            sprintf('Class property "%s" %s static.', $propertyName,
                (!$propertyIsStatic ? "is set as" : "isn't")));

        // Test property accessibility
        echo sprintf('-Test that property "%s" is %s.', $propertyName, $propertyAccessMode) . PHP_EOL;
        $customFailMessage = sprintf('Class property "%s" accessibility isn\'t %s.',
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
                TestCase::fail(sprintf('Class property "%s" accessibility is unconfirmed.', $propertyName));
        }

        // Test property type
        echo sprintf('-Test that property "%s" is type %s.', $propertyName, $propertyValueType) . PHP_EOL;
        if ($reflectedProp->hasType()) {
            $actualType = $reflectedProp->getType()->getName();
        } else {
            $actualType = 'unset';
        }
        if ($actualType === 'unset' && $propertyValueType === 'mixed') {
            $actualType = 'mixed';
        }
        TestCase::assertSame($propertyValueType, $actualType,
            sprintf('Class property "%s" type isn\'t %s.', $propertyName, $propertyValueType));

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
            echo sprintf('-Test that property "%s" default is %s.', $propertyName,
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
                sprintf('Class property "%s" default isn\'t %s.', $propertyName, $defaultValuePrintable));

        } else {
            echo sprintf('-Test that property "%s" is in position #%d in constructor.',
                    $propertyName, $positionInConstructor) . PHP_EOL;
            // Test for inconsistencies
            TestCase::assertTrue($propertyDefaultValue === 'unset',
                sprintf('Class property "%s" shouldn\'t have %s',
                    $propertyName, 'a default value if it\'s initialized by the constructor.'));

            TestCase::assertGreaterThanOrEqual(1, $positionInConstructor,
                sprintf('Class property "%s" parameter "positionInConstructor" is wrong, %s %s',
                    $propertyName, 'position should be 1 or greater.',
                    'Please review your test configuration.'));

            // Test property position in constructor
            $expectedValue = '2.2';
            if ($propertyValueType !== 'unset' && $propertyValueType !== 'mixed') {
                try {
                    settype($expectedValue, $propertyValueType);
                } catch (Error $e) {
                    TestCase::fail(sprintf('We were not able to %s "%s" with type %s.',
                        'calculate expected value for class property', $propertyName, $propertyValueType));
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
                TestCase::fail(sprintf('Class property "%s" parameter "positionInConstructor" is wrong, %s %s',
                    $propertyName, 'position should be from 1 to n parameters.',
                    'Please review your test configuration.'));
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
                sprintf('Class property "%s" position in constructor isn\'t %d.',
                    $propertyName, $positionInConstructor));
        }
    }

    /**
     * Test that a class, interface or trait exist.
     *
     * @param string $fullName The name including namespace.
     * @return void
     */
    protected function utilityExistsClassTraitOrInterface(string $fullName): void
    {
        echo PHP_EOL;

        $classExits = class_exists($fullName);
        $interfaceExits = interface_exists($fullName);
        $traitExits = trait_exists($fullName);

        // Test class, interface or trait exist
        echo sprintf('-Test that class "%s" exist.', basename($fullName)) . PHP_EOL;
        TestCase::assertTrue($classExits || $interfaceExits || $traitExits,
            sprintf("Class \"%s\" wasn't found.", $fullName));

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
    public function utilityTestClassTraitOrInterface(string $classFullName,
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
        $this->utilityExistsClassTraitOrInterface($classFullName);

        // Get ready to use Reflection utilities
        $reflectedClass = new ReflectionClass($classFullName);

        // Test class namespace
        echo sprintf('-Test that class "%s" has namespace: %s.', $classShortName, $classNameSpace) . PHP_EOL;
        TestCase::assertSame($classNameSpace, $reflectedClass->getNamespaceName(),
            sprintf('Class "%s" namespace isn\'t %s.', $classShortName, $classNameSpace));


        // Todo: Add more tests here.

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

}