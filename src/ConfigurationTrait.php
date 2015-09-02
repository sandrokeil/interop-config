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
 * ConfigurationTrait which retrieves options from configuration
 *
 * Use this trait if you want to retrieve options from a configuration and optional to perform a mandatory option check
 */
trait ConfigurationTrait
{
    /**
     * @see \Interop\Config\ObtainOptions::options
     */
    public function options($config)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '$config parameter provided to "%s" must be an "%s" or "%s"',
                    __METHOD__,
                    'array',
                    'ArrayAccess'
                )
            );
        }

        $id = null;

        if ($this instanceof HasContainerId) {
            $id = $this->containerId();
        }

        $vendorName = $this->vendorName();
        $componentName = $this->componentName();

        // this is the quickest way to determine a configuration error (performance)
        if (!isset($config[$vendorName][$componentName][$id])) {
            if (!isset($config[$vendorName][$componentName])) {
                if (empty($config[$vendorName])) {
                    throw new Exception\RuntimeException(
                        sprintf('No vendor configuration "%s" available', $vendorName)
                    );
                }
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options set in configuration "' . "['%s']['%s']",
                    $vendorName,
                    $componentName
                ));
            }
            if (null !== $id) {
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options set in configuration "' . "['%s']['%s']['%s']",
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
        if ($this instanceof HasMandatoryOptions) {
            foreach ($this->mandatoryOptions() as $option) {
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

    /**
     * @see \Interop\Config\HasConfig::vendorName
     */
    abstract public function vendorName();

    /**
     * @see \Interop\Config\HasConfig::componentName
     */
    abstract public function componentName();
}
