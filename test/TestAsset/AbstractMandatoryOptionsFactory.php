<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2014 - 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurableFactoryTrait;
use Interop\Config\MandatoryOptionsInterface;

abstract class AbstractMandatoryOptionsFactory implements
    MandatoryOptionsInterface
{
    use ConfigurableFactoryTrait;
}
