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
     * @inheritdoc \Interop\Config\RequiresConfig::canRetrieveOptions
     */
    public function canRetrieveOptions($config)
    {
        foreach ($this->dimensions() as $dimension) {
            if ((!is_array($config) && !$config instanceof ArrayAccess) || !isset($config[$dimension])) {
                return false;
            }
            $config = $config[$dimension];
        }
        return is_array($config) || $config instanceof ArrayAccess;
    }

    /**
     * @inheritdoc \Interop\Config\RequiresConfig::options
     */
    public function options($config)
    {
        foreach ($this->dimensions() as $dimension) {
            if (!is_array($config) && !$config instanceof ArrayAccess) {
                throw Exception\InvalidArgumentException::invalidConfiguration($this->dimensions(), $dimension);
            }

            if (!isset($config[$dimension])) {
                throw Exception\OptionNotFoundException::missingOptions($this->dimensions(), $dimension);
            }
            $config = $config[$dimension];
        }

        if (!is_array($config) && !$config instanceof ArrayAccess) {
            throw Exception\UnexpectedValueException::invalidOptions($this->dimensions());
        }

        if ($this instanceof RequiresMandatoryOptions) {
            $this->checkMandatoryOptions($this->mandatoryOptions(), $config);
        }

        if ($this instanceof ProvidesDefaultOptions) {
            $config = array_replace_recursive($this->defaultOptions(), $config);
        }
        return $config;
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
     * Override this function to implement your own dimension option level. Checks for RequiresConfig and
     * RequiresContainerId implementation at default.
     *
     * @return array|ArrayAccess
     */
    public function dimensions()
    {
        $dimensions = [];

        if ($this instanceof RequiresPackageConfig) {
            $dimensions = [$this->vendorName(), $this->packageName()];

            if ($this instanceof RequiresContainerId) {
                $dimensions[] = $this->containerId();
            }
        }
        return $dimensions;
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
            $useRecursion = !is_scalar($mandatoryOption);

            if ($useRecursion && isset($options[$key])) {
                $this->checkMandatoryOptions($mandatoryOption, $options[$key]);
                return;
            }

            if (!$useRecursion && isset($options[$mandatoryOption])) {
                continue;
            }

            throw Exception\MandatoryOptionNotFoundException::missingOption(
                $this->dimensions(),
                $useRecursion ? $key : $mandatoryOption
            );
        }
    }
}
