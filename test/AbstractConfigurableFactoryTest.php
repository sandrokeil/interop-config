<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config;

use InteropTest\Config\AbstractBaseTestCase as BaseTestCase;
use InteropTest\Config\TestAsset\ContainerInterop;
use SebastianBergmann\PeekAndPoke\Proxy;

/**
 * Class AbstractConfigurableFactoryTest
 *
 * Tests integrity of Interop\Config\AbstractConfigurableFactory
 */
class AbstractConfigurableFactoryAbstractBaseTest extends BaseTestCase
{
    /**
     * Class under test
     *
     * @var string
     */
    protected $cut = 'Interop\Config\AbstractConfigurableFactory';

    /**
     * Tests getOptions() should throw exception if no config is available
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldThrowNotFoundExceptionIfContainerHasNoConfig()
    {
        $stub = parent::getStub($this->cut, 'invalid_module');

        $this->setExpectedException('Interop\Config\Exception\NotFoundException', 'Could not');

        $Proxy = new Proxy($stub);
        return $Proxy->getOptions(new ContainerInterop([]));
    }

    /**
     * Tests getOptions() should throw exception if no config is available
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldThrowRuntimeExceptionIfNoConfigIsAvailable()
    {
        $stub = parent::getStub($this->cut, 'invalid_module');

        $this->setExpectedException('Interop\Config\Exception\RuntimeException', 'No configuration');

        $this->callGetOptions($stub);
    }

    /**
     * Tests getOptions() should throw exception if no option is available
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldThrowRuntimeExceptionIfNoOptionIsAvailable()
    {
        $stub = parent::getStub($this->cut, 'sake_doctrine', 'orm_manager', 'invalid');

        $this->setExpectedException('Interop\Config\Exception\OptionNotFoundException', 'Options with name');

        $this->callGetOptions($stub);
    }

    /**
     * Tests if getOptions() works as expected
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldReturnExpectedData()
    {
        $stub = parent::getStub($this->cut);
        $options = $this->callGetOptions($stub);

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);
    }

    /**
     * Tests if getOptions() works with mandatory options interface
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldCheckMandatoryOptions()
    {
        $stub = parent::getStub('InteropTest\Config\TestAsset\AbstractMandatoryOptionsFactory');

        $stub->expects($this->once())
            ->method('getMandatoryOptions')
            ->will($this->returnValue(['driverClass', 'params']));


        $options = $this->callGetOptions($stub);

        $this->assertArrayHasKey('driverClass', $options);
        $this->assertArrayHasKey('params', $options);
    }

    /**
     * Tests if getOptions() throws a runtime exception if mandatory option is missing
     *
     * @covers Interop\Config\AbstractConfigurableFactory::getOptions
     */
    public function testGetOptionsShouldThrowRuntimeExceptionIfMandatoryOptionIsMissing()
    {
        $stub = parent::getStub('InteropTest\Config\TestAsset\AbstractMandatoryOptionsFactory');

        $stub->expects($this->once())
            ->method('getMandatoryOptions')
            ->will($this->returnValue(['invalid']));

        $this->setExpectedException(
            'Interop\Config\Exception\MandatoryOptionNotFoundException',
            'Mandatory option "invalid"'
        );

        $this->callGetOptions($stub);
    }

    /**
     * Calls protected method getOptions()
     *
     * @param $stub
     *
     * @return array Options
     */
    protected function callGetOptions($stub)
    {
        $Proxy = new Proxy($stub);
        return $Proxy->getOptions($this->serviceManager);
    }
}
