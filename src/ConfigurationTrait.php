<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config;

use ArrayAccess;
use Interop\Config\Exception;

/**
 * ConfigurationTrait which retrieves options from configuration, see interface \Interop\Config\RequiresConfig
 *
 * This trait is a implementation of \Interop\Config\RequiresConfig. Retrieves options from a configuration and optional
 * to perform a mandatory option check. Default options are merged and overridden of the provided options.
 */
trait ConfigurationTrait
{
    /**
     * @inheritdoc \Interop\Config\RequiresConfig::dimensions
     */
    abstract public function dimensions();

    /**
     * Checks if options are available depending on implemented interfaces and checks that the retrieved options are an
     * array or have implemented \ArrayAccess.
     *
     * The RequiresConfigId interface is supported.
     *
     * @param array|ArrayAccess $config Configuration
     * @param string|null $configId Config name, must be provided if factory uses RequiresConfigId interface
     * @return bool True if options are available, otherwise false
     */
    public function canRetrieveOptions($config, $configId = null)
    {
        $dimensions = $this->dimensions();

        if ($this instanceof RequiresConfigId) {
            $dimensions[] = $configId;
        }

        foreach ($dimensions as $dimension) {
            if ((!is_array($config) && !$config instanceof ArrayAccess)
                || !isset($config[$dimension])
            ) {
                return false;
            }
            $config = $config[$dimension];
        }
        return is_array($config) || $config instanceof ArrayAccess;
    }

    /**
     * Returns options based on dimensions() like [vendor][package] and can perform mandatory option checks if
     * class implements RequiresMandatoryOptions. If the ProvidesDefaultOptions interface is implemented, these options
     * must be overridden by the provided config. If you want to allow configurations for more then one instance use
     * RequiresConfigId interface.
     *
     * The RequiresConfigId interface is supported.
     *
     * @param array|ArrayAccess $config Configuration
     * @param string $configId Config name, must be provided if factory uses RequiresConfigId interface
     * @return array|ArrayAccess
     */
    public function options($config, $configId = null)
    {
        $dimensions = $this->dimensions();

        if ($this instanceof RequiresConfigId) {
            $dimensions[] = $configId;
        } elseif ($configId !== null) {
            throw new Exception\InvalidArgumentException(
                sprintf('The factory "%s" does not support multiple instances.', __CLASS__)
            );
        }

        // get configuration for provided dimensions
        foreach ($dimensions as $dimension) {
            if (!is_array($config) && !$config instanceof ArrayAccess) {
                throw Exception\UnexpectedValueException::invalidOptions($dimensions, $dimension);
            }

            if (!isset($config[$dimension])) {
                throw Exception\OptionNotFoundException::missingOptions($this, $dimension, $configId);
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
     * @param string $configId Config name, must be provided if factory uses RequiresConfigId interface
     * @return array|ArrayAccess options Default options or an empty array
     */
    public function optionsWithFallback($config, $configId = null)
    {
        $options = [];

        if ($this->canRetrieveOptions($config, $configId)) {
            $options = $this->options($config, $configId);
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
