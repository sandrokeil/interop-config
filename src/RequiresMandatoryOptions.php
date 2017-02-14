<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config;

/**
 * RequiresMandatoryOptions interface
 *
 * Use this interface if you have mandatory options which should be checked on retrieving options
 */
interface RequiresMandatoryOptions
{
    /**
     * Returns a list of mandatory options which must be available
     *
     * @return iterable List with mandatory options, can be nested
     */
    public function mandatoryOptions(): iterable;
}
