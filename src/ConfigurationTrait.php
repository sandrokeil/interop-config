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
        $packageName = $this->packageName();

        // this is the fastest way to determine a configuration error (performance)
        if (!isset($config[$vendorName][$packageName][$id])) {
            if (!isset($config[$vendorName][$packageName])) {
                if (empty($config[$vendorName])) {
                    throw new Exception\RuntimeException(
                        sprintf('No vendor configuration "%s" available', $vendorName)
                    );
                }
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options set in configuration "' . "['%s']['%s']",
                    $vendorName,
                    $packageName
                ));
            }
            if (null !== $id) {
                throw new Exception\OptionNotFoundException(sprintf(
                    'No options set in configuration "' . "['%s']['%s']['%s']",
                    $vendorName,
                    $packageName,
                    $id
                ));
            }
        }
        $options = $config[$vendorName][$packageName];

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
                        $packageName,
                        $id ? '[' . $id . ']' : ''
                    ));
                }
            }
        }
        // check for default options
        if ($this instanceof HasDefaultOptions) {
            $options = array_replace_recursive($this->defaultOptions(), $options);
        }
        return $options;
    }

    /**
     * @see \Interop\Config\HasConfig::vendorName
     */
    abstract public function vendorName();

    /**
     * @see \Interop\Config\HasConfig::packageName
     */
    abstract public function packageName();
}
