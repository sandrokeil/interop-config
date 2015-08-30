# Interop Configuration

> You want to configure your factories?

> You want to check automatically for mandatory params?

> You want to have a uniform config structure?

> This library comes to the rescue!

[![Build Status](https://travis-ci.org/sandrokeil/interop-config.png?branch=master)](https://travis-ci.org/sandrokeil/interop-config)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/sandrokeil/interop-config/badges/quality-score.png?s=cdef161c14156e3e36ed0ce3d6fd7979d38d916c)](https://scrutinizer-ci.com/g/sandrokeil/interop-config/)
[![Coverage Status](https://coveralls.io/repos/sandrokeil/interop-config/badge.png?branch=master)](https://coveralls.io/r/sandrokeil/interop-config?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/sandrokeil/interop-config.svg)](http://hhvm.h4cc.de/package/sandrokeil/interop-config)
[![PHP 7 ready](http://php7ready.timesplinter.ch/sandrokeil/interop-config/badge.svg)](https://travis-ci.org/sandrokeil/interop-config)
[![Latest Stable Version](https://poser.pugx.org/sandrokeil/interop-config/v/stable.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Dependency Status](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e/badge.png)](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e)
[![Total Downloads](https://poser.pugx.org/sandrokeil/interop-config/downloads.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![License](https://poser.pugx.org/sandrokeil/interop-config/license.png)](https://packagist.org/packages/sandrokeil/interop-config)

*InteropConfig* provides interfaces and classes to create instances depending on configuration with mandatory param check and uniform config structure.

 * **Well tested.** Besides unit test and continuous integration/inspection this solution is also ~~ready for production use~~.
 * **Framework agnostic** This PHP library does not depends on any framework but you can use it with your favourite framework.
 * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/sandrokeil/interop-config/blob/master/CHANGELOG.md)
 * **Listen to your ideas.** Have a great idea? Bring your tested pull request or open a new issue.

You should have coding conventions and you should have config conventions. If not, you should think about that.

The config keys should have the following structure `vendor.component.id`.  A common configuration looks like that:

```php
// interop config example
return [
    // vendor name
    'doctrine' => [
        // component name
        'connection' => [
            // container id
            'orm_default' => [
                // mandatory params
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],
    ],
];
```

So `doctrine` is the module, `connection` is the scope and `orm_default` is the name. After that the specified instance options follow.
With [AbstractConfigurableFactory](docs/Configurable.md) we can easily access to these options and mandatory options check. 
See [docs](docs/Configurable.md) for a detailed explanation.

```php
use Interop\Config\ConfigurableFactoryTrait;
use Interop\Config\MandatoryOptionsInterface;

class MyDBALConnectionFactory implements MandatoryOptionsInterface
{
    use ConfigurableFactoryTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->getOptions($container->get('config'), 'doctrine', 'connection', 'orm_default');

        // mandatory options check is automatically done by MandatoryOptionsInterface

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }

    /**
     * Returns a list of mandatory options which must be available
     *
     * @return array
     */
    public function getMandatoryOptions()
    {
        return [
            'driverClass',
            'params',
        ];
    }
}
```

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Put the following into your composer.json

    {
        "require": {
            "sandrokeil/interop-config": "1.0.x-dev"
        }
    }

## Documentation

You can find documentation about the usages of factories at the following links:

 * [Configurable - Get an array of options and with mandatory options check](docs/Configurable.md)

