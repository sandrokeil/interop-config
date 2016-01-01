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
     *   return ['doctrine', 'connection', 'orm_default'];
     * </code>
     *
     * @return array|ArrayAccess
     */
    public function dimensions();

    /**
     * Returns options based on dimensions() like [vendor][package][id] and can perform mandatory option checks if
     * class implements RequiresMandatoryOptions. If the ProvidesDefaultOptions interface is implemented, these options
     * must be overridden by the provided config.
     *
     * This example uses RequiresContainerId interface
     *
     * <code>
     * return [
     *      // vendor name
     *     'doctrine' => [
     *          // package name
     *          'connection' => [
     *             // container id, is optional
     *             'orm_default' => [
     *                 // mandatory options, is optional
     *                 'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
     *                 'params' => [
     *                     // default options, is optional
     *                     'host' => 'localhost',
     *                     'port' => '3306',
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
     * @throws Exception\UnexpectedValueException If the $config parameter has the wrong type
     * @throws Exception\OptionNotFoundException If no options are available
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
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
