<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropBench\Config;

use InteropTest\Config\TestAsset\ConnectionContainerIdConfiguration;

class ContainerIdBench extends BaseCase
{
    /**
     * @inheritdoc \InteropBench\Config\BaseCase::getFactoryClass
     */
    protected function getFactoryClass()
    {
        return new ConnectionContainerIdConfiguration();
    }
}
