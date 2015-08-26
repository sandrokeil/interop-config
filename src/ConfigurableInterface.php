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
 * Interface ConfigurableInterface
 *
 * Interface to retrieve config options.
 */
interface ConfigurableInterface
{
    /**
     * Module/Library name
     *
     * @return string
     */
    public function getModule();

    /**
     * Config scope
     *
     * @return string
     */
    public function getScope();

    /**
     * Config name
     *
     * @return string
     */
    public function getName();
}
