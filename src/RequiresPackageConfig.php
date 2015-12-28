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
 * RequiresPackageConfig interface is for a array config structure like [vendor][package]
 *
 * Use this interface if you want to retrieve options from a configuration with the structure "vendor.package".
 */
interface RequiresPackageConfig extends RequiresConfig
{
    /**
     * Returns the vendor name
     *
     * @return string
     */
    public function vendorName();

    /**
     * Returns the package name
     *
     * @return string
     */
    public function packageName();
}
