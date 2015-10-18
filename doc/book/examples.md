# Examples

This files contains examples for each interface. The factory class uses the `ConfigurationTrait` to retrieve options 
from a configuration and optional to perform a mandatory option check or merge default options.

Let's assume we have the following module configuration:

```php
// interop config example
return [
    // vendor name
    'doctrine' => [
        // package name
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

## Retrieving options
Then you have easily access to the `orm_default` options in your method with this trait.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresContainerId;

class MyDBALConnectionFactory implements RequiresContainerId
{
    use ConfigurableFactoryTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->options($container->get('config'));
        
        // check if mandatory options are available or use \Interop\Config\RequiresMandatoryOptions, see below 
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

    public function packageName()
    {
        return 'connection';
    }

    public function containerId()
    {
        return 'orm_default';
    }
}
```

## Mandatory options check
You can also check for mandatory options automatically with `MandatoryOptionsInterface`. Now we want also check that
option `driverClass` and `params` are available. So we also implement in the example above the interface
`RequiresMandatoryOptions`. If one of these options is missing, an exception is raised.

```php
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\RequiresContainerId;

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
     * Returns a list of mandatory options which must be available
     *
     * @return string[] List with mandatory options
     */
    public function mandatoryOptions()
    {
        return [
            'driverClass',
            'params',
        ];
    }
    
    public function vendorName()
    {
        return 'doctrine';
    }

    public function packageName()
    {
        return 'connection';
    }

    public function containerId()
    {
        return 'orm_default';
    }
}
```

## Default options
Use the `RequiresDefaultOptions` interface if you have default options. These options are merged with the provided options in
`\Interop\Config\RequiresConfig::options()`. Let's look at this example from 
[DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule/blob/master/docs/configuration.md#how-to-use-two-connections). 
All the options under the key *orm_crawler* are optional, but it's not visible in the factory.

```php
return [
    'doctrine' => [
        'configuration' => [
            'orm_crawler' => [
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'hydration_cache'   => 'array',
            ],
        ],
    ],
];
```

```php
class ConfigurationFactory implements RequiresContainerId, RequiresDefaultOptions
{
    use ConfigurationTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.configuration.orm_crawler
        $options = $this->options($container->get('config'));

        # these keys are always available now 
        $options['metadata_cache'];
        $options['query_cache'];
        $options['result_cache'];
        $options['hydration_cache'];

        // create your instance and set options

        return $instance;
    }
    
    /**
     * Returns a list of default options, which are merged in \Interop\Config\RequiresConfig::options
     *
     * @return string[] List with default options and values
     */
    public function defaultOptions()
    {
        return [
            'metadata_cache' => 'array',
            'query_cache' => 'array',
            'result_cache' => 'array',
            'hydration_cache' => 'array',
        ];
    }
    
    public function vendorName()
    {
        return 'doctrine';
    }

    public function packageName()
    {
        return 'configuration';
    }

    public function containerId()
    {
        return 'orm_crawler';
    }
}
```
