<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config;

use Interop\Container\ContainerInterface;

/**
 * AbstractConfigurableFactory which retrieves configuration options from container
 *
 * Use this class if you want to retrieve the configuration options and setup your instance manually.
 */
abstract class AbstractConfigurableFactory
{
    /**
     * Returns options from "config" based on [module][scope][name] and can perform mandatory parameter checks if
     * class implements MandatoryOptionsInterface.
     *
     * @param  ContainerInterface $container The container which contains the "config" entry
     * @return mixed options
     *
     * @throws Exception\RuntimeException If no configuration was found
     * @throws Exception\OptionNotFoundException If no options are available
     * @throws Exception\MandatoryOptionNotFoundException If a mandatory option is missing
     */
    protected function getOptions(ContainerInterface $container)
    {
        if (!$container->has('config')) {
            throw new Exception\NotFoundException('Could not retrieve "config" from container');
        }
        $options = $container->get('config');

        if (empty($options[$this->getModule()])) {
            throw new Exception\RuntimeException(sprintf('No configuration "%s" available', $this->getModule()));
        }

        if (!isset($options[$this->getModule()][$this->getScope()][$this->getName()])) {
            throw new Exception\OptionNotFoundException(sprintf(
                'Options with name "%s" was not found in configuration "' . "['%s']['%s']",
                $this->getName(),
                $this->getModule(),
                $this->getScope()
            ));
        }

        $options = $options[$this->getModule()][$this->getScope()][$this->getName()];

        // check for mandatory options
        if ($this instanceof MandatoryOptionsInterface) {
            foreach ($this->getMandatoryOptions() as $option) {
                if (!isset($options[$option])) {
                    throw new Exception\MandatoryOptionNotFoundException(sprintf(
                        'Mandatory option "%s" was not set for configuration "' . "['%s']['%s']['%s']",
                        $option,
                        $this->getModule(),
                        $this->getScope(),
                        $this->getName()
                    ));
                }
            }
        }
        return $options;
    }

    /**
     * Module/Library name
     *
     * @return string
     */
    abstract protected function getModule();

    /**
     * Config scope
     *
     * @return string
     */
    abstract protected function getScope();

    /**
     * Config name
     *
     * @return string
     */
    abstract protected function getName();
}
