<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;

/**
 * InvalidArgumentException exception
 *
 * Use this exception if an argument has not the expected value.
 */
class InvalidArgumentException extends PhpInvalidArgumentException implements ExceptionInterface
{
    /**
     * @param array|\ArrayAccess $dimensions
     * @param mixed $currentDimension Current configuration key
     * @return InvalidArgumentException
     */
    public static function invalidConfiguration($dimensions, $currentDimension)
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
                implode('.', $position)
            )
        );
    }
}
