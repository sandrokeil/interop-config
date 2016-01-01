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
 * Option not found exception
 *
 * Use this exception if an option was not found in the config
 */
class OptionNotFoundException extends OutOfBoundsException
{
    /**
     * @param array|\ArrayAccess $dimensions
     * @param mixed $currentDimension Current configuration key
     * @return InvalidArgumentException
     */
    public static function missingOptions($dimensions, $currentDimension)
    {
        $position = [];

        foreach ($dimensions as $dimension) {
            if ($dimension === $currentDimension) {
                $position[] = $dimension;
                break;
            }
            $position[] = $dimension;
        }

        return new static(sprintf('No options set for configuration "%s"', implode('.', $position)));
    }
}
