# ConfigurationTrait

Use this trait if you want to retrieve options from a configuration and optional to perform a mandatory option check

Let's assume we have the following module configuration:

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

> Note that the configuration above is injected as `$config` in `options()`

## Array Options
Then you have easily access to the `orm_default` options in your method with this trait.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\HasContainerId;

class MyDBALConnectionFactory implements HasContainerId
{
    use ConfigurableFactoryTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'));
        
        // check if mandatory options are available or use \Interop\Config\HasMandatoryOptions, see below 
        if (empty($options['driverClass'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Driver class was not set for configuration %s.%s.%s',
                    'doctrine', 
                    'connection', 
                    'orm_default'
                )
            );
        }

        if (empty($options['params'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Params was not set for configuration %s.%s.%s',
                    'doctrine', 
                    'connection', 
                    'orm_default'
                )
            );
        }

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }
    
    public function vendorName()
    {
        return 'doctrine';
    }

    public function componentName()
    {
        return 'connection';
    }

    public function containerId()
    {
        return 'orm_default';
    }
}
```

## Mandatory Options check
You can also check for mandatory options automatically with `MandatoryOptionsInterface`. Now we want also check that
option `driverClass` and `params` are available. So we also implement in the example above the interface
`HasMandatoryOptions`. If one of these options is missing, an exception is raised.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\HasMandatoryOptions;
use Interop\Config\HasContainerId;

class MyDBALConnectionFactory implements HasContainerId, HasMandatoryOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'));

        // mandatory options check is automatically done by HasMandatoryOptions

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
