<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace InteropTest\Config;

use Interop\Config\Exception\InvalidArgumentException;
use Interop\Config\Exception\MandatoryOptionNotFoundException;
use Interop\Config\Exception\OptionNotFoundException;
use Interop\Config\Exception\UnexpectedValueException;
use InteropTest\Config\TestAsset\ConnectionConfiguration;
use InteropTest\Config\TestAsset\ConnectionContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionDefaultOptionsConfiguration;
use InteropTest\Config\TestAsset\ConnectionDefaultOptionsMandatoryContainetIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveArrayIteratorContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveContainerIdConfiguration;
use InteropTest\Config\TestAsset\FlexibleConfiguration;
use InteropTest\Config\TestAsset\PackageDefaultAndMandatoryOptionsConfiguration;
use InteropTest\Config\TestAsset\PackageDefaultOptionsConfiguration;
use InteropTest\Config\TestAsset\PlainConfiguration;
use InteropTest\Config\TestAsset\UniversalContainerIdConfiguration;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * ConfigurationTraitTest
 *
 * Tests integrity of \Interop\Config\ConfigurationTrait
 */
class ConfigurationTraitTest extends TestCase
{
    /**
     * Class under test
     *
     * @var string
     */
    protected $cut = 'Interop\Config\ConfigurationTrait';

    /**
     * Tests options() should throw exception if config parameter is not an array
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray(): void
    {
        $stub = new ConnectionConfiguration();

        $this->assertException(UnexpectedValueException::class, 'position is "doctrine"');

        $stub->options(['doctrine' => new \stdClass()]);
    }

    /**
     * Tests canRetrieveOptions() : void
     *
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testCanRetrieveOptions(): void
    {
        $stub = new ConnectionConfiguration();

        self::assertSame(false, $stub->canRetrieveOptions(''));
        self::assertSame(false, $stub->canRetrieveOptions(new \stdClass()));
        self::assertSame(false, $stub->canRetrieveOptions(null));

        self::assertSame(
            false,
            $stub->canRetrieveOptions(['doctrine' => ['invalid' => ['default' => ['params' => '']]]])
        );

        self::assertSame(
            false,
            $stub->canRetrieveOptions(['doctrine' => ['connection' => new \stdClass()]])
        );

        self::assertSame(true, $stub->canRetrieveOptions($this->getTestConfig()));
    }

    /**
     * Tests canRetrieveOptions() : void
     *
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testCanRetrieveOptionsWithContainerId(): void
    {
        $stub = new ConnectionContainerIdConfiguration();

        self::assertSame(false, $stub->canRetrieveOptions(['doctrine' => ['connection' => null]]), 'orm_default');

        self::assertSame(
            false,
            $stub->canRetrieveOptions(['doctrine' => ['connection' => ['invalid' => ['test' => 1]]]], 'orm_default')
        );

        self::assertSame(
            false,
            $stub->canRetrieveOptions(
                [
                    'doctrine' => [
                        'connection' => [
                            'orm_default' => new \stdClass(),
                        ],
                    ],
                ],
                'orm_default'
            )
        );

        self::assertSame(true, $stub->canRetrieveOptions($this->getTestConfig(), 'orm_default'));
    }

    /**
     * Tests options() should throw exception if config id is provided but RequiresConfigId interface is not implemented
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsInvalidArgumentExIfConfigIdIsProvidedButRequiresConfigIdIsNotImplemented(): void
    {
        $stub = new ConnectionConfiguration();

        self::assertFalse($stub->canRetrieveOptions(['doctrine' => []], 'configId'));

        $this->assertException(InvalidArgumentException::class, 'The factory');

        $stub->options(['doctrine' => []], 'configId');
    }

    /**
     * Tests options() should throw exception if config id is missing but RequiresConfigId interface is implemented
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfConfigIdIsMissingWithRequiresConfigId($config): void
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->assertException(OptionNotFoundException::class, 'The configuration');

        self::assertFalse($stub->canRetrieveOptions($config, null));

        $stub->options($config, null);
    }

    /**
     * Tests options() should throw exception if no vendor config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoVendorConfigIsAvailable(): void
    {
        $stub = new ConnectionConfiguration();

        $config = ['doctrine' => []];

        self::assertFalse($stub->canRetrieveOptions($config));

        $this->assertException(OptionNotFoundException::class, 'doctrine');

        $stub->options($config);
    }

    /**
     * Tests options() should throw exception if no package option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoPackageOptionIsAvailable(): void
    {
        $stub = new ConnectionConfiguration();

        $config = ['doctrine' => ['connection' => null]];

        self::assertFalse($stub->canRetrieveOptions($config));

        $this->assertException(
            OptionNotFoundException::class,
            'No options set for configuration "doctrine.connection"'
        );

        $stub->options($config);
    }

    /**
     * Tests options() should throw exception if no container id option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoContainerIdOptionIsAvailable(): void
    {
        $stub = new ConnectionContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => null]]];

        self::assertFalse($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(OptionNotFoundException::class, '"doctrine.connection.orm_default"');

        $stub->options($config, 'orm_default');
    }

    /**
     * Tests options() should throw exception if a dimension is not available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfDimensionIsNotAvailable(): void
    {
        $stub = new FlexibleConfiguration();

        $config = ['one' => ['two' => ['three' => ['invalid' => ['dimension']]]]];

        self::assertFalse($stub->canRetrieveOptions($config));

        $this->assertException(OptionNotFoundException::class, '"one.two.three.four"');

        $stub->options(['one' => ['two' => ['three' => ['invalid' => ['dimension']]]]]);
    }

    /**
     * Tests if options() works with dimensions, default options and mandatory options if no config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsExceptionIfMandatoryOptionsWithDefaultOptionsSetAndNoConfigurationIsSet(): void
    {
        $stub = new PackageDefaultAndMandatoryOptionsConfiguration();

        self::assertFalse($stub->canRetrieveOptions([]));

        $this->assertException(OptionNotFoundException::class, '"vendor"');

        $stub->options([]);
    }

    /**
     * Tests options() should throw exception if retrieved options not an array or \ArrayAccess
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\UnexpectedValueException::invalidOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsUnexpectedValueExceptionIfRetrievedOptionsNotAnArrayOrArrayAccess(): void
    {
        $stub = new ConnectionContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => new \stdClass()]]];

        self::assertFalse($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(UnexpectedValueException::class, 'Configuration must either be of');

        $stub->options($config, 'orm_default');
    }

    /**
     * Tests if options() works with container id
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithContainerId($config): void
    {
        $stub = new ConnectionContainerIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $options = $stub->options($config, 'orm_default');

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() works without container id
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsData($config): void
    {
        $stub = new ConnectionConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config));

        $options = $stub->options($config);

        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with flexible dimensions
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithFlexibleDimensions($config): void
    {
        $stub = new FlexibleConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config));

        $options = $stub->options($config);

        self::assertArrayHasKey('name', $options);
        self::assertArrayHasKey('class', $options);
    }

    /**
     * Tests if options() works with no dimensions
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithNoDimensions($config): void
    {
        $stub = new PlainConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config));

        $options = $stub->options($config);

        self::assertArrayHasKey('doctrine', $options);
        self::assertArrayHasKey('one', $options);
    }

    /**
     * Tests if options() works with default options
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithDefaultOptions($config): void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        unset($config['doctrine']['connection']['orm_default']['params']['host']);
        unset($config['doctrine']['connection']['orm_default']['params']['port']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        $options = $stub->options($config, 'orm_default');
        $defaultOptions = $stub->defaultOptions();

        self::assertCount(2, $options);
        self::assertArrayHasKey('params', $options);
        self::assertSame($options['params']['host'], $defaultOptions['params']['host']);
        self::assertSame($options['params']['port'], $defaultOptions['params']['port']);
        self::assertSame(
            $options['params']['user'],
            $config['doctrine']['connection']['orm_default']['params']['user']
        );

        $config = $this->getTestConfig();

        # remove main index key
        unset($config['doctrine']['connection']['orm_default']['params']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        $options = $stub->options($config, 'orm_default');

        self::assertCount(2, $options);
        self::assertArrayHasKey('params', $options);
        self::assertSame($options['params']['host'], $defaultOptions['params']['host']);
        self::assertSame($options['params']['port'], $defaultOptions['params']['port']);
    }

    /**
     * Tests if options() works with dimensions and default options if no config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsPackageDataWithDefaultOptionsIfNoConfigurationIsSet(): void
    {
        $stub = new PackageDefaultOptionsConfiguration();

        self::assertTrue($stub->canRetrieveOptions([]));

        $expected = [
            'minLength' => 2,
            'maxLength' => 10,
        ];

        $options = $stub->options([]);

        self::assertCount(2, $options);
        self::assertSame($expected, $options);
    }

    /**
     * Tests if options() works default options and default options not override provided options
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThatDefaultOptionsNotOverrideProvidedOptions($config): void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        $options = $stub->options($config, 'orm_default');

        self::assertCount(2, $options);
        self::assertArrayHasKey('params', $options);
        self::assertSame(
            $options['params']['host'],
            $config['doctrine']['connection']['orm_default']['params']['host']
        );
        self::assertSame(
            $options['params']['port'],
            $config['doctrine']['connection']['orm_default']['params']['port']
        );
        self::assertSame(
            $options['params']['user'],
            $config['doctrine']['connection']['orm_default']['params']['user']
        );
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsChecksMandatoryOptions($config): void
    {
        $stub = new ConnectionMandatoryConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config));
        $options = $stub->options($config);

        self::assertCount(1, $options);
        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsChecksMandatoryOptionsWithContainerId($config): void
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        $options = $stub->options($config, 'orm_default');

        self::assertCount(2, $options);
        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() throws a runtime exception if mandatory option is missing
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionIsMissing($config): void
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();

        unset($config['doctrine']['connection']['orm_default']['params']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "params"');
        $stub->options($config, 'orm_default');
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionRecursiveIsMissing($config): void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        unset($config['doctrine']['connection']['orm_default']['params']['dbname']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "dbname"');
        $stub->options($config, 'orm_default');
    }

    /**
     * Tests options() with recursive mandatory options check
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithRecursiveMandatoryOptionCheck($config): void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        self::assertArrayHasKey('params', $stub->options($config, 'orm_default'));
    }

    /**
     * Tests options() with recursive mandatory options as array iterator
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithRecursiveArrayIteratorMandatoryOptionCheck($config): void
    {
        $stub = new ConnectionMandatoryRecursiveArrayIteratorContainerIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        self::assertArrayHasKey('params', $stub->options($config, 'orm_default'));
    }

    /**
     * Tests if optionsWithFallback()
     *
     * @dataProvider providerConfig
     * @covers       \Interop\Config\ConfigurationTrait::optionsWithFallback
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithFallback($config): void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        self::assertArrayHasKey('params', $stub->optionsWithFallback([]));
        self::assertArrayHasKey('params', $stub->optionsWithFallback($config, 'orm_default'));
        self::assertArrayHasKey('driverClass', $stub->optionsWithFallback($config, 'orm_default'));

        unset($config['doctrine']['connection']['orm_default']['params']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        self::assertArrayHasKey('params', $stub->optionsWithFallback($config));
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfOptionsAreEmpty(): void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => []]]];

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "params"');

        $stub->options($config, 'orm_default');
    }

    /**
     * Tests if options() works with an empty \ArrayAccess object and default options.
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testEmptyArrayAccessWithDefaultOptions()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $config = new \ArrayIterator([]);

        $options = $stub->options($config);

        self::assertCount(1, $options);
        self::assertArrayHasKey('params', $options);

        self::assertSame(
            $options['params']['host'],
            'awesomehost'
        );
        self::assertSame(
            $options['params']['port'],
            '4444'
        );
    }

    /**
     * Tests if options() works with iterable objects like \ArrayIterator or \ArrayObject
     *
     * @dataProvider providerConfigObjects
     * @covers       \Interop\Config\ConfigurationTrait::options
     * @covers       \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithObjects($config, $type): void
    {
        $stub = new UniversalContainerIdConfiguration($type);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        $options = $stub->options($config, 'orm_default');

        self::assertCount(2, $options);
        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);

        $driverClass = 'Doctrine\DBAL\Driver\PDOMySql\Driver';
        $host = 'localhost';
        $dbname = 'database';
        $user = 'username';
        $password = 'password';
        $port = '4444';

        if ($type !== UniversalContainerIdConfiguration::TYPE_ONLY_ITERATOR) {
            $driverClass = $config['doctrine']['connection']['orm_default']['driverClass'];
            $host = $config['doctrine']['connection']['orm_default']['params']['host'];
            $dbname = $config['doctrine']['connection']['orm_default']['params']['dbname'];
            $user = $config['doctrine']['connection']['orm_default']['params']['user'];
            $password = $config['doctrine']['connection']['orm_default']['params']['password'];
        }

        self::assertSame($options['driverClass'], $driverClass);
        self::assertSame($options['params']['host'], $host);
        self::assertSame($options['params']['port'], $port);
        self::assertSame($options['params']['dbname'], $dbname);
        self::assertSame($options['params']['user'], $user);
        self::assertSame($options['params']['password'], $password);
    }

    public function providerConfig(): array
    {
        return [
            [$this->getTestConfig()],
            [new \ArrayObject($this->getTestConfig())],
            [new \ArrayIterator($this->getTestConfig())],
        ];
    }

    public function providerConfigObjects(): array
    {
        return [
            [$this->getTestConfig(), UniversalContainerIdConfiguration::TYPE_ARRAY_ARRAY],
            [new \ArrayObject($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_ARRAY],
            [new \ArrayIterator($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_ARRAY],

            [$this->getTestConfig(), UniversalContainerIdConfiguration::TYPE_ARRAY_OBJECT],
            [new \ArrayObject($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_OBJECT],
            [new \ArrayIterator($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_OBJECT],

            [$this->getTestConfig(), UniversalContainerIdConfiguration::TYPE_ARRAY_ITERATOR],
            [new \ArrayObject($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_ITERATOR],
            [new \ArrayIterator($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ARRAY_ITERATOR],

            [$this->getTestConfig(), UniversalContainerIdConfiguration::TYPE_ONLY_ITERATOR],
            [new \ArrayObject($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ONLY_ITERATOR],
            [new \ArrayIterator($this->getTestConfig()), UniversalContainerIdConfiguration::TYPE_ONLY_ITERATOR],
        ];
    }

    /**
     * Returns test config
     *
     * @return array
     */
    private function getTestConfig(): array
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load default
        if (is_readable('test/TestConfig.php')) {
            $config = require 'test/testing.config.php';
        } else {
            $config = require 'test/testing.config.php.dist';
        }

        return $config;
    }

    /**
     * @param $exception
     * @param $message
     */
    private function assertException($exception, $message): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
    }
}
