<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Interop\Config\Tool;

use Interop\Config\Exception\InvalidArgumentException;

/**
 * Command to dump a configuration from a factory class
 *
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */
class ConfigDumperCommand
{
    const COMMAND_DUMP = 'dump';
    const COMMAND_ERROR = 'error';
    const COMMAND_HELP = 'help';

    const DEFAULT_SCRIPT_NAME = __CLASS__;

    const HELP_TEMPLATE = <<< EOH

<info>Usage:</info>

  %s [-h|--help|help] <configFile> <className>

<info>Arguments:</info>

  <info>-h|--help|help</info>    This usage message
  <info><configFile></info>      Path to a config file or php://stdout for which to generate configuration.
                    If the file does not exist, it will be created. If it does
                    exist, it must return an array, and the file will be
                    updated with new configuration.
  <info><className></info>       Name of the class to reflect and for which to generate
                    dependency configuration.

Reads the provided configuration file (creating it if it does not exist),
and injects it with config dependency configuration for
the provided class name, writing the changes back to the file.
EOH;

    /**
     * @var string
     */
    private $scriptName;

    /**
     * @var ConsoleHelper
     */
    private $helper;

    public function __construct($scriptName = self::DEFAULT_SCRIPT_NAME, ConsoleHelper $helper = null)
    {
        $this->scriptName = $scriptName;
        $this->helper = $helper ?: new ConsoleHelper();
    }

    /**
     * @param array $args Argument list, minus script name
     * @return int Exit status
     */
    public function __invoke(array $args): int
    {
        $arguments = $this->parseArgs($args);

        switch ($arguments->command) {
            case self::COMMAND_HELP:
                $this->help();
                return 0;
            case self::COMMAND_ERROR:
                fwrite(STDERR, $arguments->message);
                $this->help(STDERR);
                return 1;
            case self::COMMAND_DUMP:
                // fall-through
            default:
                break;
        }

        $dumper = new ConfigDumper();
        try {
            $config = $dumper->createDependencyConfig($arguments->config, $arguments->class);
        } catch (InvalidArgumentException $e) {
            $this->helper->writeErrorMessage(
                sprintf('Unable to create config for "%s": %s', $arguments->class, $e->getMessage())
            );
            $this->help(STDERR);
            return 1;
        }

        file_put_contents($arguments->configFile, $dumper->dumpConfigFile($config) . PHP_EOL);

        $this->helper->writeLine(sprintf('<info>[DONE]</info> Changes written to %s', $arguments->configFile));
        return 0;
    }

    private function parseArgs(array $args): \stdClass
    {
        if (!count($args)) {
            return $this->createHelpArgument();
        }

        $arg1 = array_shift($args);

        if (in_array($arg1, ['-h', '--help', 'help'], true)) {
            return $this->createHelpArgument();
        }

        if (!count($args)) {
            return $this->createErrorArgument('Missing class name');
        }

        $configFile = $arg1;
        switch (file_exists($configFile)) {
            case true:
                $config = require $configFile;

                if (!is_array($config)) {
                    return $this->createErrorArgument(sprintf(
                        'Configuration at path "%s" does not return an array.',
                        $configFile
                    ));
                }

                break;
            case false:
                // fall-through
            default:
                if (!is_writable(dirname($configFile)) && 'php://stdout' !== $configFile) {
                    return $this->createErrorArgument(sprintf(
                        'Cannot create configuration at path "%s"; not writable.',
                        $configFile
                    ));
                }

                $config = [];
                break;
        }

        $class = array_shift($args);

        if (!class_exists($class)) {
            return $this->createErrorArgument(sprintf(
                'Class "%s" does not exist or could not be autoloaded.',
                $class
            ));
        }

        return $this->createArguments(self::COMMAND_DUMP, $configFile, $config, $class);
    }

    private function help($resource = STDOUT): void
    {
        $this->helper->writeErrorMessage(sprintf(self::HELP_TEMPLATE, $this->scriptName));
    }

    /**
     * @param string $command
     * @param string $configFile File from which config originates, and to
     *     which it will be written.
     * @param array $config Parsed configuration.
     * @param string $class Name of class to reflect.
     * @return \stdClass
     */
    private function createArguments(string $command, string $configFile, array $config, string $class): \stdClass
    {
        return (object)[
            'command' => $command,
            'configFile' => $configFile,
            'config' => $config,
            'class' => $class,
        ];
    }

    private function createErrorArgument(string $message): \stdClass
    {
        return (object)[
            'command' => self::COMMAND_ERROR,
            'message' => $message,
        ];
    }

    private function createHelpArgument(): \stdClass
    {
        return (object)[
            'command' => self::COMMAND_HELP,
        ];
    }
}
