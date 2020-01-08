<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2020 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropBench\Config;

/**
 * @BeforeMethods({"classSetUp"})
 * @Revs(10000)
 * @Iterations(10)
 * @Warmup(2)
 * @Groups({"perf"})
 */
class ArrayPerfBench
{
    /**
     * @var array
     */
    private $config;


    /**
     * Setup config and class
     */
    public function classSetUp(): void
    {
        $this->config = $this->getTestConfig();
    }

    /**
     * @Subject
     */
    public function isArray(): bool
    {
        return is_array($this->config);
    }

    /**
     * @Subject
     */
    public function castArray(): bool
    {
        return (array)$this->config === $this->config;
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
