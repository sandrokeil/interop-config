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
 * RequiresContainerId Interface
 *
 * Use this interface if a configuration is for a specific container id.
 */
interface RequiresContainerId extends RequiresConfig
{
    /**
     * Returns the container identifier
     *
     * @return string
     */
    public function containerId();
}
