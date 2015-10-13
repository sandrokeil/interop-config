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
    public function testOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException('Interop\Config\Exception\InvalidArgumentException', '$config parameter');

        $stub->options('');
    }

    /**
     * Tests options() throws not an exception if config parameter is not an array and throwing an exception is
     * disabled
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsThrowsNoExceptionIfConfigIsNotAnArrayAndThrowingExceptionIsDisabled()
    {
        $stub = new ConnectionConfiguration();

        $this->assertSame(null, $stub->options('', false));
    }

    /**
     * Tests options() should throw exception if no vendor config is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsThrowsRuntimeExceptionIfNoVendorConfigIsAvailable()
    {
        $stub = new ConnectionConfiguration();

        $this->setExpectedException('Interop\Config\Exception\RuntimeException', 'No vendor');

        $stub->options(['doctrine' => []]);
    }

    /**
     * Tests options() should throw exception if no package option is available
     *
     * @covers \Interop\Config\ConfigurationTrait::options
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
     */
    public function testOptionsThrowsOptionNotFoundExceptionIfNoIdOptionIsAvailable()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $stub->options(['doctrine' => ['connection' => ['orm_default' => null]]]);
    }

    /**
     * Tests options() throws not an exception if no container id option is available and throwing exceptions is
     * disabled
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsThrowsNoExceptionIfNoIdOptionIsAvailableAndThrowingExceptionIsDisabled()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $this->assertSame([], $stub->options(['doctrine' => ['connection' => ['orm_default' => null]]], false, []));
    }

    /**
     * Tests if options() works with container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsReturnsDataByContainerId()
    {
        $stub = new ConnectionContainerIdConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() works without container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsReturnsData()
    {
        $stub = new ConnectionConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        $this->assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with container id
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsReturnsDataWithDefaultOptions()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $testConfig = $this->getTestConfig();

        unset($testConfig['doctrine']['connection']['orm_default']['params']['host']);
        unset($testConfig['doctrine']['connection']['orm_default']['params']['port']);

        $options = $stub->options($testConfig);

        $this->assertArrayHasKey('params', $options);
        $this->assertSame($options['params']['host'], $stub->defaultOptions()['params']['host']);
        $this->assertSame($options['params']['port'], $stub->defaultOptions()['params']['port']);
        $this->assertSame(
            $options['params']['user'],
            $testConfig['doctrine']['connection']['orm_default']['params']['user']
        );

        $testConfig = $this->getTestConfig();

        # remove main index key
        unset($testConfig['doctrine']['connection']['orm_default']['params']);

        $options = $stub->options($testConfig);

        $this->assertArrayHasKey('params', $options);
        $this->assertSame($options['params']['host'], $stub->defaultOptions()['params']['host']);
        $this->assertSame($options['params']['port'], $stub->defaultOptions()['params']['port']);
    }

    /**
     * Tests if options() works default options and default options not override provied options
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     */
    public function testOptionsThatDefaultOptionsNotOverrideProvidedOptions()
    {
        $stub = new ConnectionDefaultOptionsConfiguration();

        $testConfig = $this->getTestConfig();

        $options = $stub->options($testConfig);

        $this->assertArrayHasKey('params', $options);
        $this->assertSame(
            $options['params']['host'],
            $testConfig['doctrine']['connection']['orm_default']['params']['host']
        );
        $this->assertSame(
            $options['params']['port'],
            $testConfig['doctrine']['connection']['orm_default']['params']['port']
        );
        $this->assertSame(
            $options['params']['user'],
            $testConfig['doctrine']['connection']['orm_default']['params']['user']
        );
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsChecksMandatoryOptions()
    {
        $stub = new ConnectionMandatoryConfiguration();
        $options = $stub->options($this->getTestConfig());

        $this->assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if options() works with mandatory options interface
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsChecksMandatoryOptionsByContainerId()
    {
        $stub = new ConnectionMandatoryContainerIdConfiguration();
        $options = $stub->options($this->getTestConfig());

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);
    }

    /**
     * Tests if options() throws a runtime exception if mandatory option is missing
     *
     * @covers \Interop\Config\ConfigurationTrait::options
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsThrowsRuntimeExceptionIfMandatoryOptionIsMissing()
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
     * @covers \Interop\Config\ConfigurationTrait::checkMandatoryOptions
     */
    public function testOptionsThrowsRuntimeExceptionIfMandatoryOptionRecursiveIsMissing()
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
