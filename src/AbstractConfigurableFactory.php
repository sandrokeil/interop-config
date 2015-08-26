<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config;

use Interop\Container\ContainerInterface as ServiceLocatorInterface;

/**
 * AbstractConfigurableFactory which retrieves configuration options
 *
 * Use this class if you want to retrieve the configuration options and setup your instance manually.
 */
abstract class AbstractConfigurableFactory implements ConfigurableInterface
{
    /**
     * Gets options from configuration based on module.scope.name.
     *
     * @param  ServiceLocatorInterface $sl
     * @throws Exception\RuntimeException
     * @return array Options
     */
    protected function getOptions(ServiceLocatorInterface $sl)
    {
        $options = $sl->get('config');

        if (empty($options[$this->getModule()])) {
            throw new Exception\RuntimeException(sprintf('No configuration "%s" available.', $this->getModule()));
        }

        if (!isset($options[$this->getModule()][$this->getScope()][$this->getName()])) {
            throw new Exception\RuntimeException(sprintf(
                'Options with name "%s" could not be found in configuration "%s.%s".',
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
                    throw new Exception\RuntimeException(sprintf(
                        'Mandatory option "%s" was not set for configuration "%s.%s.%s".',
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
}
