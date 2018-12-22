<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Tool;

class AbstractConfig
{
    protected function prepareConfig(iterable $config, int $indentLevel = 1): string
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

        return sprintf("[\n%s\n%s]", implode("\n", $entries), $outerIndent);
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
