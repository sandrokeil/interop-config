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
Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message

Available commands:
  help                      Displays help for a command
  display-config            Displays dependency configuration for the provided class name
  generate-config           Generates dependency configuration for the provided class name
EOF;


switch ($command) {
    case 'display-config':
        $command = new Tool\ConfigReaderCommand();
        $status = $command($argv);
        exit($status);
    case 'generate-config':
        $command = new Tool\ConfigDumperCommand();
        $status = $command($argv);
        exit($status);
    default:
        $consoleHelper = new ConsoleHelper();
        $consoleHelper->writeErrorMessage($help);
        exit(1);
}


