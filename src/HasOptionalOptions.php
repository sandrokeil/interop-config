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
 * HasOptionalOptions Interface
 *
 * Use this interface if you have optional options. This is only used to auto discover the options for a configuration
 * file
 */
interface HasOptionalOptions
{
    /**
     * Returns a list of optional options
     *
     * @return string[] List with optional options
     */
    public function optionalOptions();
}
