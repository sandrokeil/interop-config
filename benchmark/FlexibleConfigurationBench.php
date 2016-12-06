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
use InteropTest\Config\TestAsset\FlexibleConfiguration;

class FlexibleConfigurationBench extends BaseCase
{
    protected function getFactoryClass(): RequiresConfig
    {
        return new FlexibleConfiguration();
    }

    /**
     * @Subject
     * @Groups({"config"})
     */
    public function options(): void
    {
        $this->factory->options($this->config, $this->configId);
    }

    /**
     * @Subject
     * @Groups({"config"})
     */
    public function can(): void
    {
        $this->factory->canRetrieveOptions($this->config, $this->configId);
    }

    /**
     * @Subject
     * @Groups({"config"})
     */
    public function fallback(): void
    {
        $this->factory->optionsWithFallback($this->config, $this->configId);
    }
}
