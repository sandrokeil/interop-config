<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config;

/**
 * The factory creates multiple instances depending on config id.
 *
 * Multiple instances can be created depending on the provided config id to options() method
 * Use this interface if a configuration is for a specific container id / name
 *
 * The parameter $configId must be added to the dimensions to check if options are available. The options() and
 * canRetrieveOptions() method gets the $configId parameter as the second argument.
 *
 * The dimensions() method returns
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
 */
interface RequiresConfigId extends RequiresConfig
{
}
