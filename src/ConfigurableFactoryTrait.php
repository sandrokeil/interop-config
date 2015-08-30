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
 * AbstractConfigurableFactory which retrieves configuration options from container
 *
 * Use this class if you want to retrieve the configuration options and setup your instance manually.
 */
trait ConfigurableFactoryTrait
{
    /**
     * Returns options from "config" based on [vendor][component][id] and can perform mandatory parameter checks if
     * class implements MandatoryOptionsInterface.
     *
     * <code>
     * return [
     *      // vendor name
     *     'doctrine' => [
     *          // component name
     *          'connection' => [
     *             // container id
     *             'orm_default' => [
     *                 // mandatory params
     *                 'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
     *                 'params' => [],
     *             ],
     *         ],
     *     ],
     * ];
     * </code>
     *
     * @param array|ArrayAccess $config Configuration
     * @param string $vendorName
     * @param string $componentName
     * @param string $id , optional
     * @return mixed options
     *
     * @throws Exception\RuntimeException If no configuration was found
     * @throws Exception\OptionNotFoundException If no options are available
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
     */
    protected function getOptions($config, $vendorName, $componentName, $id = null)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Parameter provided to %s must be an %s or %s',
                    __METHOD__,
                    'array',
                    'Traversable'
                )
            );
        }

        // this is the quickest way to determine a configuration error (performance)
        if (!isset($config[$vendorName][$componentName][$id])) {

            if (empty($config[$vendorName])) {
                throw new Exception\RuntimeException(
                    sprintf('No vendor configuration "%s" available', $vendorName)
                );
            }

            if (!isset($config[$vendorName][$componentName])) {
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options found in configuration "' . "['%s']['%s']",
                    $vendorName,
                    $componentName
                ));
            }
            if (null !== $id) {
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options found in configuration "' . "['%s']['%s']['%s']",
                    $vendorName,
                    $componentName,
                    $id
                ));
            }
        }

        $options = $config[$vendorName][$componentName];

        if (null !== $id) {
            $options = $options[$id];
        }

        // check for mandatory options
        if ($this instanceof MandatoryOptionsInterface) {
            foreach ($this->getMandatoryOptions() as $option) {
                if (!isset($options[$option])) {
                    throw new Exception\MandatoryOptionNotFoundException(sprintf(
                        'Mandatory option "%s" was not set for configuration "' . "['%s']['%s']%s",
                        $option,
                        $vendorName,
                        $componentName,
                        $id ? '[' . $id . ']' : ''
                    ));
                }
            }
        }
        return $options;
    }
}
