<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Tool;

use Interop\Config\Exception\InvalidArgumentException;

abstract class AbstractConfigDumper
{
    /**
     * @var ConsoleHelper
     */
    protected $helper;

    /**
     * @throws InvalidArgumentException if class name is not a string or does not exist.
     */
    protected function validateClassName(string $className): void
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                sprintf('Class name must be a string, %s given', gettype($className))
            );
        }

        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Cannot find class with name "%s".', $className));
        }
    }

    public function dumpConfigFile(iterable $config): string
    {
        return sprintf(
            static::CONFIG_TEMPLATE,
            get_class($this),
            date('Y-m-d H:i:s'),
            $this->prepareConfig($config)
        );
    }

    public function displayConfigFile(iterable $config): string
    {
        return $this->prepareConfig($config);
    }

    private function prepareConfig(iterable $config, int $indentLevel = 1): string
    {
        $indent = str_repeat(' ', $indentLevel * 4);
        $entries = [];

        foreach ($config as $key => $value) {
            $key = $this->createConfigKey($key);
            $entries[] = sprintf(
                '%s%s%s,',
                $indent,
                $key ? sprintf('%s => ', $key) : '',
                $this->createConfigValue($value, $indentLevel)
            );
        }

        $outerIndent = str_repeat(' ', ($indentLevel - 1) * 4);

        return sprintf(
            "[\n%s\n%s]",
            implode("\n", $entries),
            $outerIndent
        );
    }

    private function createConfigKey($key): string
    {
        return sprintf("'%s'", $key);
    }

    private function createConfigValue($value, int $indentLevel): string
    {
        if (is_array($value) || $value instanceof \Traversable) {
            return $this->prepareConfig($value, $indentLevel + 1);
        }

        if (is_string($value) && class_exists($value)) {
            return sprintf('\\%s::class', ltrim($value, '\\'));
        }

        return var_export($value, true);
    }
}
