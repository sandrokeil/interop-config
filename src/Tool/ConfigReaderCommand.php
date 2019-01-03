<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017-2019 Sandro Keil
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
    const COMMAND_CLI_NAME = 'display-config';

    // @codingStandardsIgnoreStart
    const HELP_TEMPLATE = <<< EOH
<info>Usage:</info>
  %s  [options] [<configFile>] [<className>]
  
<info>Options:</info>
  <value>-h, --help, help</value>       Display this help message

<info>Arguments:</info>
  <value>configFile</value>             Path to a config file for which to display options. It must return an array / ArrayObject.
  <value>className</value>              Name of the class to reflect and for which to display options.

Reads the provided configuration file and displays options for the provided class name.
EOH;
    // @codingStandardsIgnoreEnd

    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(ConsoleHelper $helper = null, ConfigReader $configReader = null)
    {
        parent::__construct($helper);
        $this->configReader = $configReader ?: new ConfigReader($this->helper);
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
                $this->helper->writeErrorLine($arguments->message);
                $this->help();
                return 1;
            case self::COMMAND_DUMP:
                // fall-through
            default:
                break;
        }

        $config = $this->configReader->readConfig($arguments->config, $arguments->class);

        $this->helper->write($this->configReader->dumpConfigFile($config) . PHP_EOL);

        return 0;
    }

    protected function checkFile(string $configFile): ?\stdClass
    {
        if (!is_readable(dirname($configFile))) {
            return $this->createErrorArgument(sprintf(
                'Cannot read configuration at path "%s".',
                $configFile
            ));
        }
        return null;
    }
}
