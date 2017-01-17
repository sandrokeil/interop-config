<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Tool;

abstract class AbstractCommand
{
    /**
     * @var ConsoleHelper
     */
    protected $helper;

    protected function help($resource = STDOUT): void
    {
        $this->helper->writeErrorMessage(sprintf(static::HELP_TEMPLATE, 'aasd'));
    }

    /**
     * @param string $command
     * @param string $configFile File from which config originates, and to
     *     which it will be written.
     * @param array $config Parsed configuration.
     * @param string $class Name of class to reflect.
     * @return \stdClass
     */
    protected function createArguments(string $command, string $configFile, array $config, string $class): \stdClass
    {
        return (object)[
            'command' => $command,
            'configFile' => $configFile,
            'config' => $config,
            'class' => $class,
        ];
    }

    protected function createErrorArgument(string $message): \stdClass
    {
        return (object)[
            'command' => static::COMMAND_ERROR,
            'message' => $message,
        ];
    }

    protected function createHelpArgument(): \stdClass
    {
        return (object)[
            'command' => static::COMMAND_HELP,
        ];
    }
}
