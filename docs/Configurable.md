# ConfigurableFactoryTrait

Use this class if you want to retrieve the configuration options and setup your instance manually.

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

> Note that the configuration above must be available in your Container Interop class (ServiceLocator/ServiceManager) as key `config`.

## Array Options
Then you have easily access to the `orm_default` options in your method with this trait.

```php
use Interop\Config\ConfigurableFactoryTrait;

class MyDBALConnectionFactory
{
    use ConfigurableFactoryTrait;
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->getOptions($container->get('config'), 'doctrine', 'connection', 'orm_default');
        
        // check if mandatory options are available or use \Interop\Config\MandatoryOptionsInterface, see below 
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
}
```

## Mandatory Options check
You can also check for mandatory options automatically with `MandatoryOptionsInterface`. Now we want also check that
option `driverClass` and `params` are available. So we also implement in the example above the interface
`MandatoryOptionsInterface`. If one of these options is missing, an exception is raised.

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
