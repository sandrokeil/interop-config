<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2020 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config;

use ArrayAccess;
use Interop\Config\Exception;
use Iterator;

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
    abstract public function dimensions(): iterable;

    /**
     * Checks if options are available depending on implemented interfaces and checks that the retrieved options from
     * the dimensions path are an array or have implemented \ArrayAccess. The RequiresConfigId interface is supported.
     *
     * `canRetrieveOptions()` returning true does not mean that `options($config)` will not throw an exception.
     * It does however mean that `options()` will not throw an `OptionNotFoundException`. Mandatory options are
     * not checked.
     *
     * @param array|ArrayAccess $config Configuration
     * @param string|null $configId Config name, must be provided if factory uses RequiresConfigId interface
     * @return bool True if options depending on dimensions are available, otherwise false
     */
    public function canRetrieveOptions($config, string $configId = null): bool
    {
        $dimensions = $this->dimensions();
        $dimensions = $dimensions instanceof Iterator ? iterator_to_array($dimensions) : $dimensions;

        if ($this instanceof RequiresConfigId) {
            $dimensions[] = $configId;
        }

        foreach ($dimensions as $dimension) {
            if (((array)$config !== $config && !$config instanceof ArrayAccess)
                || (!isset($config[$dimension]) && $this instanceof RequiresMandatoryOptions)
                || (!isset($config[$dimension]) && !$this instanceof ProvidesDefaultOptions)
            ) {
                return false;
            }
            if ($this instanceof ProvidesDefaultOptions && !isset($config[$dimension])) {
                return true;
            }

            $config = $config[$dimension];
        }
        return (array)$config === $config || $config instanceof ArrayAccess;
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
     * @throws Exception\InvalidArgumentException If the $configId parameter is provided but factory does not support it
     * @throws Exception\UnexpectedValueException If the $config parameter has the wrong type
     * @throws Exception\OptionNotFoundException If no options are available
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
     */
    public function options($config, string $configId = null)
    {
        $dimensions = $this->dimensions();
        $dimensions = $dimensions instanceof Iterator ? iterator_to_array($dimensions) : $dimensions;

        if ($this instanceof RequiresConfigId) {
            $dimensions[] = $configId;
        } elseif ($configId !== null) {
            throw new Exception\InvalidArgumentException(
                sprintf('The factory "%s" does not support multiple instances.', __CLASS__)
            );
        }

        // get configuration for provided dimensions
        foreach ($dimensions as $dimension) {
            if ((array)$config !== $config && !$config instanceof ArrayAccess) {
                throw Exception\UnexpectedValueException::invalidOptions($dimensions, $dimension);
            }

            if (!isset($config[$dimension])) {
                if (!$this instanceof RequiresMandatoryOptions && $this instanceof ProvidesDefaultOptions) {
                    break;
                }
                throw Exception\OptionNotFoundException::missingOptions($this, $dimension, $configId);
            }
            $config = $config[$dimension];
        }

        if ((array)$config !== $config && !$config instanceof ArrayAccess) {
            throw Exception\UnexpectedValueException::invalidOptions($this->dimensions());
        }

        if ($this instanceof RequiresMandatoryOptions) {
            $this->checkMandatoryOptions($this->mandatoryOptions(), $config);
        }

        if ($this instanceof ProvidesDefaultOptions) {
            $options = $this->defaultOptions();

            $config = array_replace_recursive(
                $options instanceof Iterator ? iterator_to_array($options) : (array)$options,
                (array)$config
            );
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
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
     */
    public function optionsWithFallback($config, string $configId = null)
    {
        $options = [];

        if ($this->canRetrieveOptions($config, $configId)) {
            $options = $this->options($config, $configId);
        } elseif ($this instanceof ProvidesDefaultOptions) {
            $options = $this->defaultOptions();
        }
        return $options;
    }

    /**
     * Checks if a mandatory param is missing, supports recursion
     *
     * @param iterable $mandatoryOptions
     * @param array|ArrayAccess $config
     * @throws Exception\MandatoryOptionNotFoundException
     */
    private function checkMandatoryOptions(iterable $mandatoryOptions, $config): void
    {
        foreach ($mandatoryOptions as $key => $mandatoryOption) {
            $useRecursion = !is_scalar($mandatoryOption);

            if (!$useRecursion && isset($config[$mandatoryOption])) {
                continue;
            }

            if ($useRecursion && isset($config[$key])) {
                $this->checkMandatoryOptions($mandatoryOption, $config[$key]);
                return;
            }

            throw Exception\MandatoryOptionNotFoundException::missingOption(
                $this->dimensions(),
                $useRecursion ? $key : $mandatoryOption
            );
        }
    }
}
