<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
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
     * @param array|\ArrayAccess $dimensions
     * @return UnexpectedValueException
     */
    public static function invalidOptions($dimensions)
    {
        return new static(
            sprintf(
                'Options of configuration "%s" must either be of type "array" or implement "\ArrayAccess".',
                implode('.', $dimensions)
            )
        );
    }
}
