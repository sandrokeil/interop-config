<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropBench\Config;

use Interop\Config\RequiresConfig;
use Interop\Config\RequiresConfigId;

/**
 * @BeforeMethods({"classSetUp"})
 * @Revs(10000)
 * @Iterations(10)
 * @Warmup(2)
 */
abstract class BaseCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var RequiresConfig
     */
    private $factory;

    /**
     * @var string
     */
    private $configId;

    /**
     * Returns benchmark factory class
     *
     * @return mixed
     */
    abstract protected function getFactoryClass();

    /**
     * Setup config and class
     */
    public function classSetUp()
    {
        $this->config = $this->getTestConfig();
        $this->factory = $this->getFactoryClass();
        $this->configId = $this->factory instanceof RequiresConfigId ? 'orm_default' : null;
    }

    /**
     * Retrieve options
     */
    public function benchOptions()
    {
        $this->factory->options($this->config, $this->configId);
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
