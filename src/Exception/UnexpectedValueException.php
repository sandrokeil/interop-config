<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace Interop\Config\Exception;

use UnexpectedValueException as PhpUnexpectedValueException;

/**
 * UnexpectedValueException exception
 *
 * Use this exception if a value is outside a set of values.
 */
class UnexpectedValueException extends PhpUnexpectedValueException implements ExceptionInterface
{
    /**
     * @param iterable $dimensions
     * @param mixed $currentDimension Current configuration key
     * @return UnexpectedValueException
     */
    public static function invalidOptions(iterable $dimensions, $currentDimension = null) : self
    {
        $position = [];

        foreach ($dimensions as $dimension) {
            if ($dimension === $currentDimension) {
                break;
            }
            $position[] = $dimension;
        }

        return new static(
            sprintf(
                'Configuration must either be of type "array" or implement "\ArrayAccess". ' .
                'Configuration position is "%s"',
                rtrim(implode('.', $position), '.')
            )
        );
    }
}
