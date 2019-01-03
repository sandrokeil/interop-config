<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2019 Sandro Keil
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
    protected $config;

    /**
     * @var RequiresConfig
     */
    protected $factory;

    /**
     * @var string
     */
    protected $configId;

    /**
     * Returns benchmark factory class
     *
     * @return RequiresConfig
     */
    abstract protected function getFactoryClass(): RequiresConfig;

    /**
     * Setup config and class
     */
    public function classSetUp(): void
    {
        $this->config = $this->getTestConfig();
        $this->factory = $this->getFactoryClass();
        $this->configId = $this->factory instanceof RequiresConfigId ? 'orm_default' : null;
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
            $testConfig = require 'test/testing.config.php';
        } else {
            $testConfig = require 'test/testing.config.php.dist';
        }

        return $testConfig;
    }
}
