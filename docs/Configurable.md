# AbstractConfigurableFactory

Use this class if you want to retrieve the configuration options and setup your instance manually.

Let's assume we have the following module configuration:

```php
return [
    // module
    'sake_doctrine' => [
        // scope
        'orm_manager' => [
            // name
            'orm_default' => [
                // config params
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [],
            ],
        ],
    ],
];
```

> Note that the configuration above must be available in your Container Interop class (ServiceLocator/ServiceManager) as key `config`.

## Array Options
Then you have easily access to the `orm_default` options in your `createService()` method with this factory.

```php
use Interop\Config\AbstractConfigurableFactory;
use Interop\Container\ContainerInterface as ServiceLocatorInterface;

class MyDBALConnectionFactory extends AbstractConfigurableFactory
{
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->getOptions($serviceLocator);
        
        // check if mandatory options are available or use \Sake\InteropConfig\MandatoryOptionsInterface, see below 
        if (empty($options['driverClass'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Driver class was not set for configuration %s.%s.%s',
                    $this->getModule(),
                    $this->getScope(),
                    $this->getName()
                )
            );
        }

        if (empty($options['params'])) {
            throw new Exception\RuntimeException(
                sprintf(
                    'Params was not set for configuration %s.%s.%s',
                    $this->getModule(),
                    $this->getScope(),
                    $this->getName()
                )
            );
        }

        $driverClass = $options['driverClass'];
        $params = $options['params'];

        // create your instance and set options

        return $instance;
    }

    public function getModule()
    {
        return 'doctrine';
    }

    public function getScope()
    {
        return 'connection';
    }

    public function getName()
    {
        return 'orm_default';
    }
}
```

## Mandatory Options check
You can also check for mandatory options automatically with `MandatoryOptionsInterface`. Now we want also check that
option `driverClass` and `params` are available. So we also implement in the example above the interface
`MandatoryOptionsInterface`. If one of these options is missing, an exception is raised.

```php
use Interop\Config\AbstractConfigurableFactory;
use Interop\Config\MandatoryOptionsInterface;
use Interop\Container\ContainerInterface as ServiceLocatorInterface;

class MyDBALConnectionFactory extends AbstractConfigurableFactory implements MandatoryOptionsInterface
{
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        // get options for doctrine.connection.orm_default
        $options = $this->getOptions($serviceLocator);

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

    public function getModule()
    {
        return 'doctrine';
    }

    public function getScope()
    {
        return 'connection';
    }

    public function getName()
    {
        return 'orm_default';
    }
}
```
