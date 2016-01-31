<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config;

use ArrayAccess;
use Interop\Config\Exception;

/**
 * RequiresConfig interface is the main interface to configure your instances via factories
 *
 * Use this interface if you want to retrieve options from a configuration and optional to perform a mandatory option
 * check. Default options are merged and overridden of the provided options.
 */
interface RequiresConfig
{
    /**
     * Returns the depth of the configuration array as a list. Can also be an empty array. The example structure of the
     * options() method would be an array like
     *
     * <code>
     *   return ['prooph', 'service_bus', 'command_bus'];
     * </code>
     *
     * @return array|ArrayAccess
     */
    public function dimensions();

    /**
     * Returns options based on dimensions() like [vendor][package] and can perform mandatory option checks if
     * class implements RequiresMandatoryOptions. If the ProvidesDefaultOptions interface is implemented, these options
     * must be overridden by the provided config. If you want to allow configurations for more then one instance use
     * RequiresConfigId interface.
     *
     * <code>
     * return [
     *      // vendor name
     *     'prooph' => [
     *          // package name
     *          'service_bus' => [
     *             // only one instance possible
     *             'command_bus' => [
     *                  // command bus factory options
     *                 'router' => [
     *                     'routes' => [],
     *                 ],
     *             ],
     *         ],
     *     ],
     * ];
     * </code>
     *
     * @param array|ArrayAccess $config Configuration
     * @return array|ArrayAccess options
     *
     */
    public function options($config);

    /**
     * Checks if options are available depending on implemented interfaces and checks that the retrieved options are an
     * array or have implemented \ArrayAccess.
     *
     * @param array|ArrayAccess $config Configuration
     * @return bool True if options are available, otherwise false
     */
    public function canRetrieveOptions($config);
}
