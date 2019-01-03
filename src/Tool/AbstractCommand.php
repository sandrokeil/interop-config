<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2019 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Tool;

use Interop\Config\RequiresConfig;

abstract class AbstractCommand
{
    const COMMAND_DUMP = 'dump';
    const COMMAND_ERROR = 'error';
    const COMMAND_HELP = 'help';

    /**
     * @var ConsoleHelper
     */
    protected $helper;

    public function __construct(ConsoleHelper $helper = null)
    {
        $this->helper = $helper ?: new ConsoleHelper();
    }

    protected function help($resource = STDOUT): void
    {
        $this->helper->writeErrorMessage(sprintf(static::HELP_TEMPLATE, static::COMMAND_CLI_NAME));
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

    protected function parseArgs(array $args): \stdClass
    {
        if (!count($args)) {
            return $this->createHelpArgument();
        }

        $arg1 = array_shift($args);

        if (in_array($arg1, ['-h', '--help', 'help'], true)) {
            return $this->createHelpArgument();
        }

        if (!count($args)) {
            return $this->createErrorArgument('<error>Missing class name</error>');
        }

        $class = array_shift($args);

        if (!class_exists($class)) {
            return $this->createErrorArgument(
                sprintf('<error>Class "%s" does not exist or could not be autoloaded.</error>', $class)
            );
        }

        $reflectionClass = new \ReflectionClass($class);

        if (!in_array(RequiresConfig::class, $reflectionClass->getInterfaceNames(), true)) {
            return $this->createErrorArgument(
                sprintf('<error>Class "%s" does not implement "%s".</error>', $class, RequiresConfig::class)
            );
        }

        $configFile = $arg1;
        switch (file_exists($configFile)) {
            case true:
                $config = require $configFile;

                if ($config instanceof \Iterator) {
                    $config = iterator_to_array($config);
                } elseif ($config instanceof \IteratorAggregate) {
                    $config = iterator_to_array($config->getIterator());
                }

                if (!is_array($config)) {
                    return $this->createErrorArgument(
                        sprintf('<error>Configuration at path "%s" does not return an array.</error>', $configFile)
                    );
                }

                break;
            case false:
                // fall-through
            default:
                if ($command = $this->checkFile($configFile)) {
                    return $command;
                }

                $config = [];
                break;
        }

        return $this->createArguments(self::COMMAND_DUMP, $configFile, $config, $class);
    }

    abstract protected function checkFile(string $configFile): ?\stdClass;
}
