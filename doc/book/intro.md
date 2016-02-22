# Overview

`intop-config` provides interfaces and a concrete implementation to create instances depending on configuration via 
factory classes and ensures a valid config structure. It can also be used to auto discover factories and to create
configuration files.

* Configure a vendor/package or container id
* Check for mandatory options, recursion supported
* Check if options can be retrieved from configuration
* Merging of default options, recursion supported
* Generate configuration files from factory classes

## Installation
Installation of this library uses Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Put the following into your composer.json or run `composer require sandrokeil/interop-config`.

    {
        "require": {
            "sandrokeil/interop-config": "^1.0"
        }
    }

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

> Note that the configuration above is injected as `$config` in `options()`

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
     * Is used to retrieve options from the configuration array ['doctrine' => ['connection' => ['orm_default' => []]]].
     *
     * @return []
     */
    public function dimensions()
    {
        return ['doctrine', 'connection'];
    }

    /**
     * Returns a list of mandatory options which must be available
     *
     * @return string[] List with mandatory options
     */
    public function mandatoryOptions()
    {
        return ['params' => ['user', 'password', 'dbname']];
    }
}
```
