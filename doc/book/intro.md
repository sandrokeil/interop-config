# interop-config: Overview

intop-config provides interfaces and a concrete implementation to create instances depending on configuration via 
factory classes and ensures a uniform config structure. It can also be used to auto discover factories and to create
configuration files.

* Configure a vendor package
* Configure a specific container id
* Check for mandatory options
* Check if options can be retrieved from configuration
* Merging of default options

## Config Structure
The config keys should have the following structure `vendor.package.container_id`. The `container_id` is optional and is
only neccessary if you have different instances of the same class e.g. database connection.

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
instance options follow. The following example uses [ConfigurationTrait](src/ConfigurationTrait.php) which implements 
the logic to retrieve the options from a configuration. See documentation for more details.

> Note that the configuration above is injected as `$config` in `options()`

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresContainerId;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;

class MyDBALConnectionFactory implements RequiresContainerId, RequiresMandatoryOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'));

        // mandatory options check is automatically done by RequiresMandatoryOptions

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
        return ['params' => ['user', 'password', 'dbname']];
    }
}
```
