<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config\Tool {

    function date($format)
    {
        return '2017-01-22 18:42:56';
    }

    // set error reporting
    error_reporting(E_ALL | E_STRICT);

    chdir(dirname(__DIR__));

    if (!file_exists('vendor/autoload.php')) {
        throw new \RuntimeException(
            'Unable to load dependencies. Run `php composer.phar install`'
        );
    }

    // Setup autoloading
    include 'vendor/autoload.php';
}
