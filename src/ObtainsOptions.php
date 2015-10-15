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
 * ObtainOptions Interface
 *
 * Use this interface if you want to retrieve options from a configuration and optional to perform a mandatory option
 * check. Default options are merged and overridden of the provided options.
 */
interface ObtainsOptions extends HasConfig
{
    /**
     * Returns options based on [vendor][package][id] and can perform mandatory option checks if class implements
     * MandatoryOptionsInterface. If the HasDefaultOptions interface is implemented, these options must be overriden
     * by the provided config. The HasContainerId interface is optional.
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
     * @throws Exception\OutOfBoundsException If vendor name was not found
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
