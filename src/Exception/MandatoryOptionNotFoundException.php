<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config\Exception;

/**
 * Mandatory option not found exception
 *
 * Use this exception if a mandatory option was not found in the config
 */
class MandatoryOptionNotFoundException extends OutOfBoundsException
{
}
