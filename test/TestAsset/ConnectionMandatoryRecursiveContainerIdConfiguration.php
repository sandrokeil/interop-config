<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2014 - 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\HasContainerId;
use Interop\Config\HasMandatoryOptions;
use Interop\Config\ObtainsOptions;

class ConnectionMandatoryRecursiveContainerIdConfiguration implements
    ObtainsOptions,
    HasContainerId,
    HasMandatoryOptions
{
    use ConfigurationTrait;

    public function vendorName()
    {
        return 'doctrine';
    }

    public function packageName()
    {
        return 'connection';
    }

    public function containerId()
    {
        return 'orm_default';
    }

    public function mandatoryOptions()
    {
        return ['driverClass', 'params' => ['user', 'dbname']];
    }
}
