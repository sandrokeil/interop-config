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
     * @see \Interop\Config\HasConfig::vendorName
     */
    abstract public function vendorName();

    /**
     * @see \Interop\Config\HasConfig::packageName
     */
    abstract public function packageName();

    /**
     * Returns options and it is possible to disable exceptions and return a default value instead.
     *
     * @see \Interop\Config\ObtainOptions::options
     *
     * @param $config
     * @param bool $throwException Whether or not to throw an exception, optional default true
     * @param mixed $default Value is returned of $throwException is false, optional default null
     * @return mixed options
     */
    public function options($config, $throwException = true, $default = null)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            if (false === $throwException) {
                return $default;
            }
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
            if (false === $throwException) {
                return $default;
            }
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
            $this->checkMandatoryOptions($this->mandatoryOptions(), $options);
        }
        // check for default options
        if ($this instanceof HasDefaultOptions) {
            $options = array_replace_recursive($this->defaultOptions(), $options);
        }
        return $options;
    }

    /**
     * Checks if a mandatory param is missing, supports recursion
     *
     * @param array|ArrayAccess $mandatoryOptions
     * @param array|ArrayAccess $options
     * @throws Exception\MandatoryOptionNotFoundException
     */
    private function checkMandatoryOptions($mandatoryOptions, $options)
    {
        foreach ($mandatoryOptions as $key => $mandatoryOption) {
            # if a string key exists it indicates a recursive check
            if (isset($options[$key])) {
                $this->checkMandatoryOptions($mandatoryOption, $options[$key]);
                return;
            }
            if (isset($options[$mandatoryOption])) {
                continue;
            }
            $id = null;

            if ($this instanceof HasContainerId) {
                $id = $this->containerId();
            }

            throw new Exception\MandatoryOptionNotFoundException(sprintf(
                'Mandatory option "%s" was not set for configuration "' . "['%s']['%s']%s",
                $mandatoryOption,
                $this->vendorName(),
                $this->packageName(),
                $id ? '[' . $id . ']' : ''
            ));
        }
    }
}
