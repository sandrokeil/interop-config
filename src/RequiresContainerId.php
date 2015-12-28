<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config;

/**
 * RequiresContainerId interface is for a array config structure like [vendor][package][id]
 *
 * Use this interface if a configuration is for a specific container id and if you want to retrieve options from a
 * configuration with the structure "vendor.package.id".
 */
interface RequiresContainerId extends RequiresPackageConfig
{
    /**
     * Returns the container identifier
     *
     * @return string
     */
    public function containerId();
}
