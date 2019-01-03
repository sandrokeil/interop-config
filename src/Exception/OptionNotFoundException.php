<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2019 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config\Exception;

use Interop\Config\RequiresConfig;
use Interop\Config\RequiresConfigId;

/**
 * Option not found exception
 *
 * Use this exception if an option was not found in the config
 */
class OptionNotFoundException extends OutOfBoundsException
{
    /**
     * @param RequiresConfig $factory
     * @param mixed $currentDimension Current configuration key
     * @param string $configId
     * @return OptionNotFoundException
     */
    public static function missingOptions(RequiresConfig $factory, $currentDimension, ?string $configId) : self
    {
        $position = [];

        $dimensions = $factory->dimensions();

        if ($factory instanceof RequiresConfigId) {
            $dimensions[] = $configId;
        }

        foreach ($dimensions as $dimension) {
            $position[] = $dimension;

            if ($dimension === $currentDimension) {
                break;
            }
        }

        if ($factory instanceof RequiresConfigId && $configId === null && count($dimensions) === count($position)) {
            return new static(
                rtrim(
                    sprintf('The configuration "%s" needs a config id.', implode('.', $position)),
                    '.'
                )
            );
        }

        return new static(sprintf('No options set for configuration "%s"', implode('.', $position)));
    }
}
