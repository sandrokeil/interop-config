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
use Interop\Config\RequiresMandatoryOptions;

class ConnectionMandatoryContainerIdConfiguration implements RequiresMandatoryOptions
{
    use ConfigurationTrait;

    /**
     * @interitdoc
     */
    public function dimensions()
    {
        return ['doctrine', 'connection', 'orm_default'];
    }

    public function mandatoryOptions()
    {
        return ['driverClass', 'params'];
    }
}
