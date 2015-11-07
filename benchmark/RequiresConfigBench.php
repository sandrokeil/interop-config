<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropBench\Config;

use InteropTest\Config\TestAsset\ConnectionConfiguration;

class RequiresConfigBench extends BaseCase
{
    private $config;

    /**
     * @var ConnectionConfiguration
     */
    private $factory;

    public function classSetUp()
    {
        $this->config = $this->getTestConfig();
        $this->factory = new ConnectionConfiguration();
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
