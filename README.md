# Interop Configuration

> You want to configure your factories?

> You want to check automatically for mandatory params?

> You want to have an uniform config structure?

> This library comes to the rescue!

[![Build Status](https://travis-ci.org/sandrokeil/interop-config.png?branch=master)](https://travis-ci.org/sandrokeil/interop-config)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/sandrokeil/interop-config/badges/quality-score.png?s=cdef161c14156e3e36ed0ce3d6fd7979d38d916c)](https://scrutinizer-ci.com/g/sandrokeil/interop-config/)
[![Coverage Status](https://coveralls.io/repos/sandrokeil/interop-config/badge.svg?branch=master)](https://coveralls.io/r/sandrokeil/interop-config?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/sandrokeil/interop-config.svg)](http://hhvm.h4cc.de/package/sandrokeil/interop-config)
[![PHP 7 ready](http://php7ready.timesplinter.ch/sandrokeil/interop-config/badge.svg)](https://travis-ci.org/sandrokeil/interop-config)
[![Latest Stable Version](https://poser.pugx.org/sandrokeil/interop-config/v/stable.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![Dependency Status](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e/badge.png)](https://www.versioneye.com/user/projects/53615c75fe0d0720eb00009e)
[![Total Downloads](https://poser.pugx.org/sandrokeil/interop-config/downloads.png)](https://packagist.org/packages/sandrokeil/interop-config)
[![License](https://poser.pugx.org/sandrokeil/interop-config/license.png)](https://packagist.org/packages/sandrokeil/interop-config)

*interop-config* provides interfaces and a concrete implementation to create instances depending on configuration via 
factory classes and ensures a uniform config structure. It can also be used to auto discover factories 
and to create configuration files.

> Please join the discussion about the [PSR config proposal](https://github.com/php-fig/fig-standards/pull/620).

 * **Well tested.** Besides unit test and continuous integration/inspection this solution is also ~~ready for production use~~.
 * **Framework agnostic** This PHP library does not depends on any framework but you can use it with your favourite framework.
 * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/sandrokeil/interop-config/blob/master/CHANGELOG.md)
 * **Listen to your ideas.** Have a great idea? Bring your tested pull request or open a new issue. See [CONTRIBUTING.md](CONTRIBUTING.md)

You should have coding conventions and you should have config conventions. If not, you should think about that.

The config keys should have the following structure `vendor.package.container_id`.  A common configuration looks like that:

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
instance options follow. The following example uses [ConfigurationTrait](src/ConfigurationTrait.php) which implements 
the logic to retrieve the options from a configuration. See documentation for more details.

> Note that the configuration above is injected as `$config` in `options()`

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\HasMandatoryOptions;
use Interop\Config\HasContainerId;
use Interop\Container\ContainerInterface;

class MyDBALConnectionFactory implements HasMandatoryOptions, HasContainerId
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'));

        // mandatory options check is automatically done by MandatoryOptionsInterface

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }

    /**
     * Returns the vendor name
     *
     * @return string
     */
    public function vendorName()
    {
        return 'doctrine';
    }

    /**
     * Returns the package name
     *
     * @return string
     */
    public function packageName()
    {
        return 'connection';
    }

    /**
     * Returns the container identifier
     *
     * @return string
     */
    public function containerId()
    {
        return 'orm_default';
    }

    /**
     * Returns a list of mandatory options which must be available
     *
     * @return string[] List with mandatory options
     */
    public function mandatoryOptions()
    {
        return ['driverClass', 'params'];
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

Documentation is [in the doc tree](doc/), and can be compiled using [bookdown](http://bookdown.io) and [Docker](https://www.docker.com/)

```console
$ docker run -i -t=false --rm -v $(pwd):/app sandrokeil/bookdown doc/bookdown.json
$ docker run -i -t=false --rm -p 8080:8080 -v $(pwd):/app php:5.6-cli php -S 0.0.0.0:8080 -t /app/doc/html
```

or make sure bookdown is installed globally via composer and `$HOME/.composer/vendor/bin` is on your `$PATH`.

```console
$ bookdown doc/bookdown.json
$ php -S 0.0.0.0:8080 -t doc/html/ # then browse to http://localhost:8080/
```
