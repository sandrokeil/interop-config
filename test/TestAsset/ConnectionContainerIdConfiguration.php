<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2014 - 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\HasContainerId;
use Interop\Config\ObtainsOptions;

class ConnectionContainerIdConfiguration implements ObtainsOptions, HasContainerId
{
    use ConfigurationTrait;

    public function vendorName()
    {
        return 'doctrine';
    }

    public function componentName()
    {
        return 'connection';
    }

    public function containerId()
    {
        return 'orm_default';
    }
}
