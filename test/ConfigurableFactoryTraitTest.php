<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config;

use PHPUnit_Framework_TestCase as TestCase;
use SebastianBergmann\PeekAndPoke\Proxy;

/**
 * Class ConfigurableFactoryTraitTest
 *
 * Tests integrity of Interop\Config\ConfigurableFactoryTrait
 */
class ConfigurableFactoryTraitTest extends TestCase
{
    /**
     * Class under test
     *
     * @var string
     */
    protected $cut = 'Interop\Config\ConfigurableFactoryTrait';

    /**
     * Tests getOptions() should throw exception if config parameter is not an array
     *
     * @covers \Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsThrowsInvalidArgumentExceptionIfConfigIsNotAnArray()
    {
        $stub = $this->getMockForTrait('Interop\Config\ConfigurableFactoryTrait');

        $this->setExpectedException('Interop\Config\Exception\InvalidArgumentException', 'Parameter');

        $Proxy = new Proxy($stub);
        return $Proxy->getOptions('', 'doctrine', 'connection');
    }

    /**
     * Tests getOptions() should throw exception if no vendor config is available
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsThrowsRuntimeExceptionIfNoVendorConfigIsAvailable()
    {
        $stub = $this->getMockForTrait('Interop\Config\ConfigurableFactoryTrait');

        $this->setExpectedException('Interop\Config\Exception\RuntimeException', 'No vendor');

        $Proxy = new Proxy($stub);
        return $Proxy->getOptions(['doctrine' => []], 'doctrine', 'connection');
    }

    /**
     * Tests getOptions() should throw exception if no component option is available
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsThrowsOptionNotFoundExceptionIfNoComponentOptionIsAvailable()
    {
        $stub = $this->getMockForTrait('Interop\Config\ConfigurableFactoryTrait');

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $Proxy = new Proxy($stub);
        return $Proxy->getOptions(['doctrine' => ['connection' => null]], 'doctrine', 'connection');
    }

    /**
     * Tests getOptions() should throw exception if no id option is available
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsThrowsOptionNotFoundExceptionIfNoIdOptionIsAvailable()
    {
        $stub = $this->getMockForTrait('Interop\Config\ConfigurableFactoryTrait');

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'No options');

        $Proxy = new Proxy($stub);
        return $Proxy->getOptions(
            ['doctrine' => ['connection' => ['orm_default' => null]]],
            'doctrine',
            'connection',
            'orm_default'
        );
    }

    /**
     * Tests if getOptions() works as expected
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsShouldReturnExpectedData()
    {
        $stub = $this->getMockForTrait('Interop\Config\ConfigurableFactoryTrait');

        $testConfig = $this->getTestConfig();

        $Proxy = new Proxy($stub);
        $options = $Proxy->getOptions($testConfig, 'doctrine', 'connection', 'orm_default');

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);

        $options = $Proxy->getOptions($testConfig, 'doctrine', 'connection');
        $this->assertArrayHasKey('orm_default', $options);
    }

    /**
     * Tests if getOptions() works with mandatory options interface
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsShouldCheckMandatoryOptions()
    {
        $stub = $this->getMockForAbstractClass('InteropTest\Config\TestAsset\AbstractMandatoryOptionsFactory');

        $stub->expects($this->once())
            ->method('getMandatoryOptions')
            ->will($this->returnValue(['driverClass', 'params']));

        $Proxy = new Proxy($stub);
        $options = $Proxy->getOptions($this->getTestConfig(), 'doctrine', 'connection', 'orm_default');

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);
    }

    /**
     * Tests if getOptions() throws a runtime exception if mandatory option is missing
     *
     * @covers Interop\Config\ConfigurableFactoryTrait::getOptions
     */
    public function testGetOptionsThrowsRuntimeExceptionIfMandatoryOptionIsMissing()
    {
        $stub = $this->getMockForAbstractClass('InteropTest\Config\TestAsset\AbstractMandatoryOptionsFactory');

        $stub->expects($this->once())
            ->method('getMandatoryOptions')
            ->will($this->returnValue(['invalid']));

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "invalid"'
        );

        $Proxy = new Proxy($stub);
        $Proxy->getOptions($this->getTestConfig(), 'doctrine', 'connection', 'orm_default');
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
