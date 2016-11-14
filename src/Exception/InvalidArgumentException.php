<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2016 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Interop\Config\Exception;

use InvalidArgumentException as PhpInvalidArgumentException;

/**
 * InvalidArgumentException exception
 *
 * Use this exception if an argument has not the expected value.
 */
class InvalidArgumentException extends PhpInvalidArgumentException implements ExceptionInterface
{
}
