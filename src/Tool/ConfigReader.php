<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Tool;

use Interop\Config\Exception\OptionNotFoundException;
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresConfigId;

class ConfigReader extends AbstractConfig
{
    const CONFIG_TEMPLATE = '%s;';

    /**
     * @var ConsoleHelper
     */
    private $helper;

    public function __construct(ConsoleHelper $helper = null)
    {
        $this->helper = $helper ?: new ConsoleHelper();
    }

    public function readConfig(array $config, string $className): array
    {
        $reflectionClass = new \ReflectionClass($className);

        $interfaces = $reflectionClass->getInterfaceNames();

        $factory = $reflectionClass->newInstanceWithoutConstructor();
        $dimensions = [];

        if (in_array(RequiresConfig::class, $interfaces, true)) {
            $dimensions = $factory->dimensions();
        }

        foreach ($dimensions as $dimension) {
            if (!isset($config[$dimension])) {
                throw OptionNotFoundException::missingOptions($factory, $dimension, null);
            }
            $config = $config[$dimension];
        }

        if (in_array(RequiresConfigId::class, $interfaces, true)) {
            while (true) {
                $configId = $this->helper->readLine(implode(', ', array_keys($config)), 'For which config id');

                if ('' !== $configId) {
                    if (isset($config[$configId])) {
                        return $config[$configId];
                    }
                    $this->helper->writeErrorMessage(sprintf('No config id with name "%s" exists.', $configId));
                    continue;
                }
                break;
            }
        }

        return $config;
    }

    public function dumpConfigFile(iterable $config): string
    {
        return $this->prepareConfig($config);
    }
}
