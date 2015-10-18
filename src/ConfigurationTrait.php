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
 * Use this trait if you want to retrieve options from a configuration and optional to perform a mandatory option check.
 * Default options are merged and overridden of the provided options.
 */
trait ConfigurationTrait
{
    /**
     * @see \Interop\Config\RequiresConfig::vendorName
     */
    abstract public function vendorName();

    /**
     * @see \Interop\Config\RequiresConfig::packageName
     */
    abstract public function packageName();

    /**
     * @see \Interop\Config\RequiresConfig::canRetrieveOptions
     */
    public function canRetrieveOptions($config)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            return false;
        }
        $id = null;

        if ($this instanceof RequiresContainerId) {
            $id = $this->containerId();
        }
        $vendorName = $this->vendorName();
        $packageName = $this->packageName();

        if (!isset($config[$vendorName][$packageName])
            || (null !== $id && !isset($config[$vendorName][$packageName][$id]))
        ) {
            return false;
        }
        $options = $config[$vendorName][$packageName];

        if (null !== $id) {
            $options = $options[$id];
        }
        return is_array($options) || $options instanceof ArrayAccess;
    }

    /**
     * @see \Interop\Config\RequiresConfig::options
     */
    public function options($config)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(
                sprintf('Provided parameter $config  must either be of type "array" or implement "\ArrayAccess".')
            );
        }
        $id = null;

        if ($this instanceof RequiresContainerId) {
            $id = $this->containerId();
        }
        $vendorName = $this->vendorName();
        $packageName = $this->packageName();

        // this is the fastest way to determine a configuration error (performance)
        if (!isset($config[$vendorName][$packageName][$id])) {
            if (!isset($config[$vendorName][$packageName])) {
                if (empty($config[$vendorName])) {
                    throw new Exception\OutOfBoundsException(
                        sprintf('No vendor configuration "%s" available', $vendorName)
                    );
                }
                throw new Exception\OptionNotFoundException(
                    sprintf('No options set for configuration "' . "['%s']['%s']", $vendorName, $packageName)
                );
            }
            if (null !== $id) {
                throw new Exception\OptionNotFoundException(
                    sprintf('No options set for configuration "' . "['%s']['%s']['%s']", $vendorName, $packageName, $id)
                );
            }
        }
        $options = $config[$vendorName][$packageName];

        if (null !== $id) {
            $options = $options[$id];
        }
        if (!is_array($options) && !$options instanceof ArrayAccess) {
            throw new Exception\UnexpectedValueException(
                sprintf(
                    'Options of configuration ' . "['%s']['%s']%s"
                    . ' must either be of type "array" or implement "\ArrayAccess".',
                    $this->vendorName(),
                    $this->packageName(),
                    $id ? '["' . $id . '""]' : ''
                )
            );
        }
        if ($this instanceof RequiresMandatoryOptions) {
            $this->checkMandatoryOptions($this->mandatoryOptions(), $options);
        }
        if ($this instanceof ProvidesDefaultOptions) {
            $options = array_replace_recursive($this->defaultOptions(), $options);
        }
        return $options;
    }

    /**
     * Checks if options can be retrieved from config and if not, default options (ProvidesDefaultOptions interface) or
     * an empty array will be returned.
     *
     * @param array|ArrayAccess $config Configuration
     * @return array|ArrayAccess options Default options or an empty array
     */
    public function optionsWithFallback($config)
    {
        $options = [];

        if ($this->canRetrieveOptions($config)) {
            $options = $this->options($config);
        }
        if (empty($options) && $this instanceof ProvidesDefaultOptions) {
            $options = $this->defaultOptions();
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

            if ($this instanceof RequiresContainerId) {
                $id = $this->containerId();
            }

            throw new Exception\MandatoryOptionNotFoundException(sprintf(
                'Mandatory option "%s" was not set for configuration "' . "['%s']['%s']%s",
                $mandatoryOption,
                $this->vendorName(),
                $this->packageName(),
                $id ? '["' . $id . '""]' : ''
            ));
        }
    }
}
