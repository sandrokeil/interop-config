<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;
use Interop\Config\RequiresMandatoryOptions;

class ConnectionMandatoryRecursiveContainerIdConfiguration implements RequiresConfigId, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    /**
     * @interitdoc
     */
    public function dimensions(): iterable
    {
        return new \ArrayIterator(['doctrine', 'connection']);
    }

    /**
     * @interitdoc
     */
    public function mandatoryOptions(): iterable
    {
        return new \ArrayIterator(['params' => ['user', 'dbname'], 'driverClass']);
    }
}
