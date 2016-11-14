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
use InteropTest\Config\TestAsset\ConnectionDefaultOptionsMandatoryContainetIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveArrayIteratorContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveContainerIdConfiguration;
use InteropTest\Config\TestAsset\FlexibleConfiguration;
use InteropTest\Config\TestAsset\PackageDefaultAndMandatoryOptionsConfiguration;
use InteropTest\Config\TestAsset\PackageDefaultOptionsConfiguration;
use InteropTest\Config\TestAsset\PlainConfiguration;
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
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray() : void
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
    public function testCanRetrieveOptions() : void
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
    public function testCanRetrieveOptionsWithContainerId() : void
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
                            'orm_default' => new \stdClass()
                        ]
                    ]
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
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIdIsProvidedButRequiresConfigIdIsNotImplemented() : void
    {
        $stub = new ConnectionConfiguration();

        self::assertFalse($stub->canRetrieveOptions(['doctrine' => []], 'configId'));

        $this->assertException(InvalidArgumentException::class, 'The factory');

        $stub->options(['doctrine' => []], 'configId');
    }

    /**
     * Tests options() should throw exception if config id is missing but RequiresConfigId interface is implemented
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfConfigIdIsMissingButRequiresConfigIdIsImplemented() : void
    {
        $stub = new ConnectionContainerIdConfiguration();

        $config = $this->getTestConfig();

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
    public function testOptionsThrowsOptionNotFoundExceptionIfNoVendorConfigIsAvailable() : void
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
    public function testOptionsThrowsOptionNotFoundExceptionIfNoPackageOptionIsAvailable() : void
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
    public function testOptionsThrowsOptionNotFoundExceptionIfNoContainerIdOptionIsAvailable() : void
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
    public function testOptionsThrowsOptionNotFoundExceptionIfDimensionIsNotAvailable() : void
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
    public function testOptionsThrowsExceptionIfMandatoryOptionsWithDefaultOptionsSetAndNoConfigurationIsSet() : void
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
    public function testOptionsThrowsUnexpectedValueExceptionIfRetrievedOptionsNotAnArrayOrArrayAccess() : void
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
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithContainerId() : void
    {
        $stub = new ConnectionContainerIdConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig, 'orm_default'));

        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() works without container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsData() : void
    {
        $stub = new ConnectionConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig));

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with flexible dimensions
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithFlexibleDimensions() : void
    {
        $stub = new FlexibleConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig));

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('name', $options);
        self::assertArrayHasKey('class', $options);
    }

    /**
     * Tests if options() works with no dimensions
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithNoDimensions() : void
    {
        $stub = new PlainConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig));

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('doctrine', $options);
        self::assertArrayHasKey('one', $options);
    }

    /**
     * Tests if options() works with default options
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsReturnsDataWithDefaultOptions() : void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        $testConfig = $this->getTestConfig();

        unset($testConfig['doctrine']['connection']['orm_default']['params']['host']);
        unset($testConfig['doctrine']['connection']['orm_default']['params']['port']);

        self::assertTrue($stub->canRetrieveOptions($testConfig, 'orm_default'));
        $options = $stub->options($testConfig, 'orm_default');
        $defaultOptions = $stub->defaultOptions();

        self::assertArrayHasKey('params', $options);
        self::assertSame($options['params']['host'], $defaultOptions['params']['host']);
        self::assertSame($options['params']['port'], $defaultOptions['params']['port']);
        self::assertSame(
            $options['params']['user'],
            $testConfig['doctrine']['connection']['orm_default']['params']['user']
        );

        $testConfig = $this->getTestConfig();

        # remove main index key
        unset($testConfig['doctrine']['connection']['orm_default']['params']);

        self::assertTrue($stub->canRetrieveOptions($testConfig, 'orm_default'));
        $options = $stub->options($testConfig, 'orm_default');

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
    public function testOptionsReturnsPackageDataWithDefaultOptionsIfNoConfigurationIsSet() : void
    {
        $stub = new PackageDefaultOptionsConfiguration();

        self::assertTrue($stub->canRetrieveOptions([]));

        $expected = [
            'minLength' => 2,
            'maxLength' => 10
        ];

        $options = $stub->options([]);

        self::assertSame($expected, $options);
    }

    /**
     * Tests if options() works default options and default options not override provided options
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThatDefaultOptionsNotOverrideProvidedOptions() : void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig, 'orm_default'));
        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('params', $options);
        self::assertSame(
            $options['params']['host'],
            $testConfig['doctrine']['connection']['orm_default']['params']['host']
        );
        self::assertSame(
            $options['params']['port'],
            $testConfig['doctrine']['connection']['orm_default']['params']['port']
        );
        self::assertSame(
            $options['params']['user'],
            $testConfig['doctrine']['connection']['orm_default']['params']['user']
        );
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsChecksMandatoryOptions() : void
    {
        $stub = new ConnectionMandatoryConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig));
        $options = $stub->options($testConfig);

        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsChecksMandatoryOptionsWithContainerId() : void
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();

        $testConfig = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($testConfig, 'orm_default'));
        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() throws a runtime exception if mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionIsMissing() : void
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();

        $config = $this->getTestConfig();
        unset($config['doctrine']['connection']['orm_default']['params']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "params"');
        $stub->options($config, 'orm_default');
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionRecursiveIsMissing() : void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = $this->getTestConfig();

        unset($config['doctrine']['connection']['orm_default']['params']['dbname']);

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "dbname"');
        $stub->options($config, 'orm_default');
    }

    /**
     * Tests options() with recursive mandatory options check
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithRecursiveMandatoryOptionCheck() : void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        self::assertArrayHasKey('params', $stub->options($config, 'orm_default'));
    }

    /**
     * Tests options() with recursive mandatory options as array iterator
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithRecursiveArrayIteratorMandatoryOptionCheck() : void
    {
        $stub = new ConnectionMandatoryRecursiveArrayIteratorContainerIdConfiguration();

        $config = $this->getTestConfig();

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));
        self::assertArrayHasKey('params', $stub->options($config, 'orm_default'));
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::optionsWithFallback
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     */
    public function testOptionsWithFallback() : void
    {
        $stub = new ConnectionDefaultOptionsMandatoryContainetIdConfiguration();

        $config = $this->getTestConfig();
        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        self::assertArrayHasKey('params', $stub->optionsWithFallback([]));
        self::assertArrayHasKey('params', $stub->optionsWithFallback($config, 'orm_default'));

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
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfOptionsAreEmpty() : void
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => []]]];

        self::assertTrue($stub->canRetrieveOptions($config, 'orm_default'));

        $this->assertException(MandatoryOptionNotFoundException::class, 'Mandatory option "params"');

        $stub->options($config, 'orm_default');
    }

    /**
     * Returns test config
     *
     * @return array
     */
    private function getTestConfig() : array
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load default
        if (is_readable('test/TestConfig.php')) {
            $testConfig = require 'test/testing.config.php';
        } else {
            $testConfig = require 'test/testing.config.php.dist';
        }

        return $testConfig;
    }

    /**
     * @param $exception
     * @param $message
     */
    private function assertException($exception, $message) : void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
    }
}
