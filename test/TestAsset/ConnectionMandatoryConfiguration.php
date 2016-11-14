<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;

class ConnectionMandatoryConfiguration implements RequiresConfig, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    /**
     * @interitdoc
     */
    public function dimensions(): iterable
    {
        return ['doctrine', 'connection'];
    }

    public function mandatoryOptions(): iterable
    {
        return ['orm_default'];
    }
}
