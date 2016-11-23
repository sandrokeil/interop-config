# Overview

> You want to configure your factories?

> You want to reduce your factory boilerplate code?

> You want to check automatically for mandatory options or merge default options?

> You want to have a valid config structure?

> You want to generate your configuration files from factory classes?

> This library comes to the rescue!

[![Build Status](https://travis-ci.org/sandrokeil/interop-config.png?branch=master)](https://travis-ci.org/sandrokeil/interop-config)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/sandrokeil/interop-config/badges/quality-score.png?s=cdef161c14156e3e36ed0ce3d6fd7979d38d916c)](https://scrutinizer-ci.com/g/sandrokeil/interop-config/)
[![Coverage Status](https://coveralls.io/repos/sandrokeil/interop-config/badge.svg?branch=master)](https://coveralls.io/r/sandrokeil/interop-config?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/sandrokeil/interop-config.svg?style=flat)](http://hhvm.h4cc.de/package/sandrokeil/interop-config)
[![PHP 7 ready](http://php7ready.timesplinter.ch/sandrokeil/interop-config/badge.svg)](https://travis-ci.org/sandrokeil/interop-config)
[![Latest Stable Version](https://poser.pugx.org/sandrokeil/interop-config/v/stable.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Dependency Status](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e/badge.png)](https://www.versioneye.com/php/sandrokeil:interop-config/0.3.1)
[![Total Downloads](https://poser.pugx.org/sandrokeil/interop-config/downloads.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Reference Status](https://www.versioneye.com/php/sandrokeil:interop-config/reference_badge.svg?style=flat)](https://www.versioneye.com/php/sandrokeil:interop-config/references)
[![License](https://poser.pugx.org/sandrokeil/interop-config/license.png)](https://packagist.org/packages/sandrokeil/interop-config)

`intop-config` provides interfaces and a concrete implementation to create instances depending on configuration via
factory classes and ensures a valid config structure. It can also be used to auto discover factories and to create
configuration files.

 * **Well tested.** Besides unit test and continuous integration/inspection this solution is also ready for production use.
 * **Framework agnostic** This PHP library does not depends on any framework but you can use it with your favourite framework.
 * **Every change is tracked**. Want to know whats new? Take a look at the changelog section.
 * **Listen to your ideas.** Have a great idea? Bring your tested pull request or open a new issue. See contributing section.

You should have coding conventions and you should have config conventions. If not, you should think about that.
`interop-config` is universally applicable! See further documentation for more details.

## Installation
Installation of this library uses Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Run `composer require sandrokeil/interop-config` to install interop-config.
    
It is recommended to use [container-interop](https://github.com/container-interop/container-interop) to retrieve the
configuration in your factories.

## Config Structure
> The following example is a common practice for libraries. You are free to use another config structure. See examples.

The config keys should have the following structure `vendor.package.container_id`. The `container_id` is optional and is
only necessary if you have different instances of the same class e.g. database connection.

A common configuration looks like that:

```php
// interop config example
return [
    // vendor name
    'doctrine' => [
        // package name
        'connection' => [
            // container id
            'orm_default' => [
                // mandatory options
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

So `doctrine` is the vendor, `connection` is the package and `orm_default` is the container id. After that the specified 
instance options follow. The following example uses `ConfigurationTrait` which implements the logic to retrieve the 
options from a configuration. `RequiresConfigId` interface ensures support for more than one instance.

> Note that the configuration above is injected as `$config` in `options()` and
[container-interop](https://github.com/container-interop/container-interop) is used to retrieve the application configuration.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;

class MyDBALConnectionFactory implements RequiresConfigId, RequiresMandatoryOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'), 'orm_default');

        // mandatory options check is automatically done by RequiresMandatoryOptions

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }

    /**
     * Is used to retrieve options from the configuration array ['doctrine' => ['connection' => []]].
     *
     * @return iterable
     */
    public function dimensions() : iterable
    {
        return ['doctrine', 'connection'];
    }

    /**
     * Returns a list of mandatory options which must be available
     *
     * @return iterable List with mandatory options
     */
    public function mandatoryOptions() : iterable
    {
        return ['params' => ['user', 'password', 'dbname']];
    }
}
```
