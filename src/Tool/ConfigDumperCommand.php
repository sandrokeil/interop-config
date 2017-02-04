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
class ConfigDumperCommand extends AbstractCommand
{
    const COMMAND_CLI_NAME = 'generate-config';

    const HELP_TEMPLATE = <<< EOH
<info>Usage:</info>
  %s  [options] [<configFile>] [<className>]

<info>Options:</info>
  <value>-h, --help, help</value>       Display this help message

<info>Arguments:</info>
  <value>configFile</value>             Path to a config file or php://stdout for which to generate options.
  <value>className</value>              Name of the class to reflect and for which to generate options.

Reads the provided configuration file (creating it if it does not exist), and injects it with options for the provided 
class name, writing the changes back to the file.
EOH;

    /**
     * @var ConfigDumper
     */
    private $configDumper;

    public function __construct(ConsoleHelper $helper = null, ConfigDumper $configReader = null)
    {
        parent::__construct($helper);
        $this->configDumper = $configReader ?: new ConfigDumper($this->helper);
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

        $config = $this->configDumper->createConfig($arguments->config, $arguments->class);

        $fileHeader = '';

        if (file_exists($arguments->configFile)) {
            foreach (token_get_all(file_get_contents($arguments->configFile)) as $token) {
                if (is_array($token)) {
                    if (isset($token[1]) && 'return' === $token[1]) {
                        break;
                    }
                    $fileHeader .= $token[1];
                    continue;
                }
                $fileHeader .= $token;
            }
        }

        if (empty($fileHeader)) {
            $fileHeader = ConfigDumper::CONFIG_TEMPLATE;
        }

        file_put_contents($arguments->configFile, $fileHeader . $this->configDumper->dumpConfigFile($config) . PHP_EOL);

        $this->helper->writeLine(sprintf('<info>[DONE]</info> Changes written to %s', $arguments->configFile));
        return 0;
    }

    protected function checkFile(string $configFile): ?\stdClass
    {
        if (!is_writable(dirname($configFile)) && 'php://stdout' !== $configFile) {
            return $this->createErrorArgument(sprintf(
                'Cannot create configuration at path "%s"; not writable.',
                $configFile
            ));
        }
        return null;
    }
}
