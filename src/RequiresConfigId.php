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

/**
 * The factory creates multiple instances depending on config id.
 *
 * Multiple instances can be created depending on the provided config id to options() method
 * Use this interface if a configuration is for a specific container id / name
 */
interface RequiresConfigId extends RequiresConfig
{
    /**
     * Returns options based on dimensions() like [vendor][package] and can perform mandatory option checks if
     * class implements RequiresMandatoryOptions. If the ProvidesDefaultOptions interface is implemented, these options
     * must be overridden by the provided config.
     *
     * The parameter $configId must be added to the dimensions.
     *
     * The dimensions method returns
     *
     * <code>
     *     return ['doctrine', 'connection'];
     * </code>
     *
     * The configuration looks like this
     *
     * <code>
     * return [
     *      // vendor name
     *     'doctrine' => [
     *          // package name
     *          'connection' => [
     *             // config id
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
     * @param string $configId Config name, must be provided
     * @return array|\ArrayAccess
     */
    public function options($config, $configId = null);

    /**
     * Checks if options are available depending on implemented interfaces and checks that the retrieved options are an
     * array or have implemented \ArrayAccess.
     *
     * The parameter $configId must be added to the dimensions.
     *
     * @param array|ArrayAccess $config Configuration
     * @param string $configId, must be provided if factory implements RequireConfigId interface
     * @return bool True if options are available, otherwise false
     */
    public function canRetrieveOptions($config, $configId = null);
}
