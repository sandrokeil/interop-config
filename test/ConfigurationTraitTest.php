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
     */
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException('Interop\Config\Exception\InvalidArgumentException', 'Configuration must either');

        $stub->options('');
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

        self::assertSame(false, $stub->canRetrieveOptions(['doctrine' => ['connection' => null]]));

        self::assertSame(
            false,
            $stub->canRetrieveOptions(['doctrine' => ['connection' => ['invalid' => ['test' => 1]]]])
        );

        self::assertSame(
            false,
            $stub->canRetrieveOptions(['doctrine' => ['connection' => ['orm_default' => new \stdClass()]]])
        );

        self::assertSame(true, $stub->canRetrieveOptions($this->getTestConfig()));
    }

    /**
     * Tests options() should throw exception if no vendor config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
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
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoPackageOptionIsAvailable()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $stub->options(['doctrine' => ['connection' => null]]);
    }

    /**
     * Tests options() should throw exception if no container id option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoContainerIdOptionIsAvailable()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $stub->options(['doctrine' => ['connection' => ['orm_default' => null]]]);
    }

    /**
     * Tests options() should throw exception if a dimension is not available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfDimensionIsNotAvailable()
    {
        $stub = new FlexibleConfiguration();

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $stub->options(['one' => ['two' => ['three' => ['invalid' => ['dimension']]]]]);
    }

    /**
     * Tests options() should throw exception if retrieved options not an array or \ArrayAccess
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     */
    public function testOptionsThrowsUnexpectedValueExceptionIfRetrievedOptionsNotAnArrayOrArrayAccess()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->setExpectedException('Interop\Config\Exception\UnexpectedValueException', 'Options of configuration');

        $stub->options(['doctrine' => ['connection' => ['orm_default' => new \stdClass()]]]);
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

        $options = $stub->options($testConfig);

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

        $options = $stub->options($testConfig);

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

        $options = $stub->options($testConfig);

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

        $options = $stub->options($testConfig);

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
        $options = $stub->options($this->getTestConfig());

        self::assertArrayHasKey('driverClass', $options);
        self::assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() throws a runtime exception if mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
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

        $stub->options($config);
    }

    /**
     * Tests if options() throws a runtime exception if a recursive mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::dimensions
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
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

        $stub->options($config);
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

        self::assertArrayHasKey('params', $stub->options($config));
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
     */
    public function testOptionsThrowsMandatoryOptionNotFoundExceptionIfOptionsAreEmpty()
    {
        $stub = new ConnectionMandatoryRecursiveContainerIdConfiguration();

        $config = ['doctrine' => ['connection' => ['orm_default' => []]]];

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "params"'
        );

        $stub->options($config);
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
