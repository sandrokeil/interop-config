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
class ConfigReaderCommand extends AbstractCommand
{
    const COMMAND_DUMP = 'dump';
    const COMMAND_ERROR = 'error';
    const COMMAND_HELP = 'help';

    const HELP_TEMPLATE = <<< EOH

<info>Usage:</info>

  %s [-h|--help|help] <className>

<info>Arguments:</info>

  <info>-h|--help|help</info>    This usage message
  <info><configFile></info>      Path to a config file for which to displa configuration.
                    It must return an array / ArrayObject.
  <info><className></info>       Name of the class to reflect and for which to display
                    dependency configuration.

Reads the provided configuration file and displays dependency 
configuration for the provided class name.
EOH;

    public function __construct(ConsoleHelper $helper = null)
    {
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

        $dumper = new ConfigReader();

        try {
            $config = $dumper->displayDependencyConfig($arguments->config, $arguments->class);
        } catch (InvalidArgumentException $e) {
            $this->helper->writeErrorMessage(
                sprintf('Unable to create config for "%s": %s', $arguments->class, $e->getMessage())
            );
            $this->help(STDERR);
            return 1;
        }

        fwrite(STDOUT, $dumper->displayConfigFile($config) . PHP_EOL);

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

                if ($config instanceof \Iterator) {
                    $config = iterator_to_array($config);
                } elseif ($config instanceof \IteratorAggregate) {
                    $config = iterator_to_array($config->getIterator());
                }

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
                if (!is_readable(dirname($configFile))) {
                    return $this->createErrorArgument(sprintf(
                        'Cannot read configuration at path "%s".',
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
}
