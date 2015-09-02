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
 * HasConfig Interface
 *
 * Use this interface if you want to use a configuration
 */
interface HasConfig
{
    /**
     * Returns the vendor name
     *
     * @return string
     */
    public function vendorName();

    /**
     * Returns the component name
     *
     * @return string
     */
    public function componentName();
}
