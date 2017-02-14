<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config;

// Setup/verify autoloading
use Interop\Config\Tool\ConsoleHelper;

if (file_exists($a = getcwd() . '/vendor/autoload.php')) {
    require $a;
} elseif (file_exists($a = __DIR__ . '/../../../autoload.php')) {
    require $a;
} elseif (file_exists($a = __DIR__ . '/../vendor/autoload.php')) {
    require $a;
} else {
    fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
    exit(1);
}

$argv = array_slice($argv, 1);

$command = array_shift($argv);

$help = <<<EOF
<info>Usage:</info>
  command [options] [arguments]

<info>Options:</info>
  <value>-h, --help, help</value>          Display this help message

<info>Available commands:</info>
  <value>generate-config</value>           Generates options for the provided class name
  <value>display-config</value>            Displays current options for the provided class name
EOF;

try {
    switch ($command) {
        case 'display-config':
            $command = new Tool\ConfigReaderCommand();
            $status = $command($argv);
            exit($status);
        case 'generate-config':
            $command = new Tool\ConfigDumperCommand();
            $status = $command($argv);
            exit($status);
        case '-h':
        case '--help':
        case 'help':
            $consoleHelper = new ConsoleHelper();
            $consoleHelper->writeLine($help);
            exit(0);
        default:
            $consoleHelper = new ConsoleHelper();
            $consoleHelper->writeErrorMessage(strip_tags($help));
            exit(1);
    }
} catch (\Throwable $e) {
    $consoleHelper = new ConsoleHelper();
    $consoleHelper->writeErrorMessage($e->getMessage());
    $consoleHelper->writeLine($help);
    exit(1);
}
