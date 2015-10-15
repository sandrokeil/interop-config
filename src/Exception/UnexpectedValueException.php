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
 * Use this exception if the code attempts to access an associative array, but performs a check for the key.
 */
class UnexpectedValueException extends PhpUnexpectedValueException implements ExceptionInterface
{
}
