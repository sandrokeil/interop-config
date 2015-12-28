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
        try {
            $options = $this->determineOptions($config, $this->dimensions());
        } catch (Exception\InvalidArgumentException $exception) {
            return false;
        } catch (Exception\OutOfBoundsException $exception) {
            return false;
        }
        return is_array($options) || $options instanceof ArrayAccess;
    }

    /**
     * @inheritdoc \Interop\Config\RequiresConfig::options
     */
    public function options($config)
    {
        $options = $this->determineOptions($config, $this->dimensions());

        if (!is_array($options) && !$options instanceof ArrayAccess) {
            throw new Exception\UnexpectedValueException(
                sprintf(
                    'Options of configuration ' . "%s"
                    . ' must either be of type "array" or implement "\ArrayAccess".',
                    implode('.', $this->dimensions())
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
     * Determine recursively options depending on given dimensions
     *
     * @param array|ArrayAccess $config
     * @param array|ArrayAccess $dimensions
     * @return array|ArrayAccess
     * @throws Exception\InvalidArgumentException
     * @throws Exception\OptionNotFoundException
     */
    private function determineOptions($config, $dimensions)
    {
        if (!is_array($config) && !$config instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(
                sprintf('Provided parameter $config  must either be of type "array" or implement "\ArrayAccess".')
            );
        }
        $dimension = array_shift($dimensions);

        if (!isset($config[$dimension])) {
            $depth = array_diff($this->dimensions(), array($dimension));

            throw new Exception\OptionNotFoundException(
                sprintf('No options set for configuration "%s"', implode('.', $depth))
            );
        }

        return empty($dimensions)
            ? $config[$dimension]
            : $this->determineOptions($config[$dimension], $dimensions);
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
            throw new Exception\MandatoryOptionNotFoundException(sprintf(
                'Mandatory option "%s" was not set for configuration "' . "%s",
                $useRecursion ? $key : $mandatoryOption,
                implode('.', $this->dimensions())
            ));
        }
    }
}
