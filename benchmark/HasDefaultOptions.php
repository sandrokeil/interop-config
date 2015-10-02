<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropBench\Config;

use Athletic\AthleticEvent;
use InteropTest\Config\TestAsset\ConnectionDefaultOptionsConfiguration;

class HasDefaultOptions extends AthleticEvent
{
    private $config;

    /**
     * @var ConnectionDefaultOptionsConfiguration
     */
    private $factory;

    public function classSetUp()
    {
        $this->config = $this->getTestConfig();
        $this->factory = new ConnectionDefaultOptionsConfiguration();
    }

    /**
     * Retrieve options
     *
     * @iterations 10000
     */
    public function options()
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
