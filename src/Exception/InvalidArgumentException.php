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
}
