<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropBench\Config;

use Interop\Config\RequiresConfig;
use InteropTest\Config\TestAsset\ConnectionMandatoryRecursiveContainerIdConfiguration;

class RequiresMandatoryOptionsRecursiveContainerIdBench extends BaseCase
{
    protected function getFactoryClass(): RequiresConfig
    {
        return new ConnectionMandatoryRecursiveContainerIdConfiguration();
    }

    /**
     * @Subject
     * @Groups({"configId", "mandatoryRec"})
     */
    public function options(): void
    {
        $this->factory->options($this->config, $this->configId);
    }

    /**
     * @Subject
     * @Groups({"configId", "mandatoryRec"})
     */
    public function can(): void
    {
        $this->factory->canRetrieveOptions($this->config, $this->configId);
    }

    /**
     * @Subject
     * @Groups({"configId", "mandatoryRec"})
     */
    public function fallback(): void
    {
        $this->factory->optionsWithFallback($this->config, $this->configId);
    }
}
