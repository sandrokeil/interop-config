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

use RuntimeException as PhpRuntimeException;

/**
 * Runtime exception
 *
 * Use this exception if the code has not the capacity to handle the request.
 */
class RuntimeException extends PhpRuntimeException implements ExceptionInterface
{
}
