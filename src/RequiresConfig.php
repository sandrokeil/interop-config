<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
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
     * Returns the depth of the configuration array as a list. Can also be an empty array. For instance, the structure
     * of the dimensions() method would be an array like
     *
     * <code>
     *   return ['prooph', 'service_bus', 'command_bus'];
     * </code>
     *
     * @return array|ArrayAccess
     */
    public function getDimensions();

    /**
     * Returns options based on dimensions() like [vendor][package] and can perform mandatory option checks if
     * class implements RequiresMandatoryOptions. If the ProvidesDefaultOptions interface is implemented, these options
     * must be overridden by the provided config. If you want to allow configurations for more then one instance use
     * RequiresConfigId interface and add an optional second parameter named $configId. The ConfigurationTrait supports
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
     * @throws Exception\InvalidArgumentException If the $configId parameter is provided but factory does not support it
     * @throws Exception\UnexpectedValueException If the $config parameter has the wrong type
     * @throws Exception\OptionNotFoundException If no options are available
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
     */
    public function getOptions($config);

    /**
     * Checks if options are available depending on implemented interfaces and checks that the retrieved options are an
     * array or have implemented \ArrayAccess. The ConfigurationTrait supports RequiresConfigId interface.
     *
     * @param array|ArrayAccess $config Configuration
     * @return bool True if options are available, otherwise false
     */
    public function canRetrieveOptions($config);
}
