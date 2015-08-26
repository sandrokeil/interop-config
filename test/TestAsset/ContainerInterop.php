<?php
/**
 * Sake
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.txt New BSD License
 */

namespace InteropTest\Config\TestAsset;

use Interop\Container\ContainerInterface;

class ContainerInterop implements ContainerInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get($id)
    {
        return $this->config[$id];
    }

    public function has($id)
    {
        return isset($this->config[$id]);
    }
}
