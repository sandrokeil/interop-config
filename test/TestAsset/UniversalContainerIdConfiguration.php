<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2020 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

namespace InteropTest\Config\TestAsset;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfigId;
use Interop\Config\RequiresMandatoryOptions;

class UniversalContainerIdConfiguration implements RequiresConfigId, ProvidesDefaultOptions, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    const TYPE_ARRAY_ITERATOR = 0;
    const TYPE_ARRAY_OBJECT = 1;
    const TYPE_ARRAY_ARRAY = 2;
    const TYPE_ONLY_ITERATOR = 3;

    private static $dimensions = [
        'doctrine',
        'universal',
    ];

    private static $mandatoryOptions = [
        'params' => ['user', 'dbname'],
        'driverClass',
    ];

    private static $defaultOptions = [
        'params' => [
            'host' => 'awesomehost',
            'port' => 4444,
        ],
    ];

    private $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function dimensions(): iterable
    {
        return $this->getData('dimensions');
    }

    public function mandatoryOptions(): iterable
    {
        return $this->getData('mandatoryOptions');
    }

    public function defaultOptions(): iterable
    {
        return $this->getData('defaultOptions');
    }

    private function getData($name)
    {
        switch ($this->type) {
            case self::TYPE_ARRAY_ITERATOR:
                return new \ArrayIterator(self::$$name);
                break;
            case self::TYPE_ARRAY_OBJECT:
                return new \ArrayObject(self::$$name);
                break;
            case self::TYPE_ONLY_ITERATOR:
                return new OnlyIterator(self::$$name);
                break;
            case self::TYPE_ARRAY_ARRAY:
            default:
                return self::$$name;
                break;
        }
    }
}
