<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config;

use InteropTest\Config\TestAsset\ConnectionConfiguration;
use InteropTest\Config\TestAsset\ConnectionContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionDefaultOptionsConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryContainerIdConfiguration;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveContainerIdConfiguration;
use InteropTest\Config\TestAsset\FlexibleConfiguration;
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
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\InvalidArgumentException::invalidConfiguration
     */
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\UnexpectedValueException',
            'position is "doctrine"'
        );

        $stub->options(['doctrine' => new \stdClass()]);
    }

    /**
     * Tests canRetrieveOptions()
     *
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testCanRetrieveOptions()
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
     * Tests canRetrieveOptions()
     *
     * @covers \Interop\Config\ConfigurationTrait::canRetrieveOptions
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testCanRetrieveOptionsWithContainerId()
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
     * Tests options() should throw exception if no vendor config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoVendorConfigIsAvailable()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'doctrine');

        $stub->options(['doctrine' => []]);
    }

    /**
     * Tests options() should throw exception if no package option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoPackageOptionIsAvailable()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\OptionNotFoundException',
            'No options set for configuration "doctrine.connection"'
        );

        $stub->options(['doctrine' => ['connection' => null]]);
    }

    /**
     * Tests options() should throw exception if no container id option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoContainerIdOptionIsAvailable()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\OptionNotFoundException',
            '"doctrine.connection.orm_default"'
        );

        $stub->options(['doctrine' => ['connection' => ['orm_default' => null]]], 'orm_default');
    }

    /**
     * Tests options() should throw exception if a dimension is not available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\OptionNotFoundException::missingOptions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfDimensionIsNotAvailable()
    {
        $stub = new FlexibleConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\OptionNotFoundException',
            '"one.two.three.four"'
        );

        $stub->options(['one' => ['two' => ['three' => ['invalid' => ['dimension']]]]]);
    }

    /**
     * Tests options() should throw exception if retrieved options not an array or \ArrayAccess
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\Exception\UnexpectedValueException::invalidOptions
     */
    public function testOptionsThrowsUnexpectedValueExceptionIfRetrievedOptionsNotAnArrayOrArrayAccess()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\UnexpectedValueException',
            'Configuration must either be of'
        );

        $stub->options(['doctrine' => ['connection' => ['orm_default' => new \stdClass()]]], 'orm_default');
    }

    /**
     * Tests if options() works with container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsReturnsDataWithContainerId()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() works without container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsReturnsData()
    {
        $stub = new ConnectionConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with flexible dimensions
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsReturnsDataWithFlexibleDimensions()
    {
        $stub = new FlexibleConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('name', $options);
        self::assertArrayHasKey('class', $options);
    }

    /**
     * Tests if options() works with no dimensions
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsReturnsDataWithNoDimensions()
    {
        $stub = new PlainConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        self::assertArrayHasKey('doctrine', $options);
        self::assertArrayHasKey('one', $options);
    }

    /**
     * Tests if options() works with container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsReturnsDataWithDefaultOptions()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $testConfig = $this->getTestConfig();

        unset($testConfig['doctrine']['connection']['orm_default']['params']['host']);
        unset($testConfig['doctrine']['connection']['orm_default']['params']['port']);

        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('params', $options);
        self::assertSame($options['params']['host'], $stub->defaultOptions()['params']['host']);
        self::assertSame($options['params']['port'], $stub->defaultOptions()['params']['port']);
        self::assertSame(
            $options['params']['user'],
            $testConfig['doctrine']['connection']['orm_default']['params']['user']
        );

        $testConfig = $this->getTestConfig();

        # remove main index key
        unset($testConfig['doctrine']['connection']['orm_default']['params']);

        $options = $stub->options($testConfig, 'orm_default');

        self::assertArrayHasKey('params', $options);
        self::assertSame($options['params']['host'], $stub->defaultOptions()['params']['host']);
        self::assertSame($options['params']['port'], $stub->defaultOptions()['params']['port']);
    }

    /**
     * Tests if options() works default options and default options not override provided options
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsThatDefaultOptionsNotOverrideProvidedOptions()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $testConfig = $this->getTestConfig();

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
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsChecksMandatoryOptions()
    {
        $stub = new ConnectionMandatoryConfiguration();
        $options = $stub->options($this->getTestConfig());

        self::assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsChecksMandatoryOptionsWithContainerId()
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();
        $options = $stub->options($this->getTestConfig(), 'orm_default');

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() throws a runtime exception if mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionIsMissing()
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "params"'
        );

        $config = $this->getTestConfig();
        unset($config['doctrine']['connection']['orm_default']['params']);

        $stub->options($config, 'orm_default');
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfMandatoryOptionRecursiveIsMissing()
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "dbname"'
        );

        $config = $this->getTestConfig();

        unset($config['doctrine']['connection']['orm_default']['params']['dbname']);

        $stub->options($config, 'orm_default');
    }

    /**
     * Tests options() with recursive mandatory options check
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsWithRecursiveMandatoryOptionCheck()
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = $this->getTestConfig();

        self::assertArrayHasKey('params', $stub->options($config, 'orm_default'));
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::optionsWithFallback
     */
    public function testOptionsWithFallback()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $config = $this->getTestConfig();

        self::assertArrayHasKey('params', $stub->optionsWithFallback([]));
        self::assertArrayHasKey('params', $stub->optionsWithFallback($config));

        unset($config['doctrine']['connection']['orm_default']['params']);

        self::assertArrayHasKey('params', $stub->optionsWithFallback($config));
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     * @covers \Interop\Config\Exception\MandatoryOptionNotFoundException::missingOption
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfOptionsAreEmpty()
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => []]]];

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "params"'
        );

        $stub->options($config, 'orm_default');
    }

    /**
     * Returns test config
     *
     * @return array
     */
    private function getTestConfig()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load default
        if (is_readable('test/TestConfig.php')) {
            $testConfig = require 'test/testing.config.php';
        } else {
            $testConfig = require 'test/testing.config.php.dist';
        }

        return $testConfig;
    }
}
