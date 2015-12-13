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
    protected function getFactoryClass()
    {
        return new ConnectionConfiguration();
    }
}
