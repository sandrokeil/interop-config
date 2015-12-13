<?php

namespace InteropBench\Config;

use Interop\Config\RequiresConfig;

/**
 * @BeforeMethods({"classSetUp"})
 * @Revs(10000)
 * @Iterations(10)
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
    }

    /**
     * Retrieve options
     */
    public function benchOptions()
    {
        $this->factory->options($this->config);
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
