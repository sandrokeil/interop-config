<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2019 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;

class ConnectionDefaultOptionsConfiguration implements RequiresConfig, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    public function dimensions(): iterable
    {
        return ['doctrine', 'connection'];
    }

    public function defaultOptions(): iterable
    {
        return [
            'params' => [
                'host' => 'awesomehost',
                'port' => 4444,
            ],
        ];
    }
}
