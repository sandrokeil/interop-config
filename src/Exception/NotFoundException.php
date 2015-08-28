<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace Interop\Config\Exception;

use Interop\Container\Exception\NotFoundException as ContainerNotFoundException;

/**
 * Not found exception
 *
 * Use this exception if an id was not found in the container
 */
class NotFoundException extends RuntimeException implements ContainerNotFoundException
{
}
