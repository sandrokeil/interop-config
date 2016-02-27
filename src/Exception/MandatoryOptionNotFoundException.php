<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Exception;

/**
 * Mandatory option not found exception
 *
 * Use this exception if a mandatory option was not found in the config
 */
class MandatoryOptionNotFoundException extends OutOfBoundsException
{
    /**
     * @param array|\ArrayAccess $dimensions
     * @param mixed $option Missed option
     * @return UnexpectedValueException
     */
    public static function missingOption($dimensions, $option)
    {
        return new static(
            sprintf('Mandatory option "%s" was not set for configuration "%s"', $option, implode('.', $dimensions))
        );
    }
}
