<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config\Exception;

/**
 * Mandatory option not found exception
 *
 * Use this exception if a mandatory option was not found in the config
 */
class MandatoryOptionNotFoundException extends OutOfBoundsException
{
    public static function missingOption(iterable $dimensions, $option) : self
    {
        $depth = '';

        foreach ($dimensions as $dimension) {
            $depth .= '.' . $dimension;
        }
        return new static(
            sprintf('Mandatory option "%s" was not set for configuration "%s"', $option, $depth)
        );
    }
}
