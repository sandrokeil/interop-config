<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropBench\Config;

use InteropTest\Config\TestAsset\ConnectionMandatoryConfiguration;

/**
 * @beforeMethod classSetUp
 */
class HasMandatoryOptions
{
    private $config;

    /**
     * @var ConnectionMandatoryConfiguration
     */
    private $factory;

    public function classSetUp()
    {
        $this->config = $this->getTestConfig();
        $this->factory = new ConnectionMandatoryConfiguration();
    }

    /**
     * Retrieve options
     *
     * @revs 1000
     * @iterations 10
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
