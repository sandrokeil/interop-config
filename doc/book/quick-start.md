# Quick Start
Typically you will have a factory which creates a concrete instance depending on some options (dependencies).

## `RequiresConfig` interface
Let's say *My factory requires a configuration* so you will implement the `RequiresConfig` interface.

```php
use Interop\Config\RequiresConfig;

class MyAwesomeFactory implements RequiresConfig
{
    public function dimensions()
    {
        return ['vendor-package'];
    }
    
    public function canRetrieveOptions($config)
    {
        // custom implementation depending on specifications
    }
    
    public function options($config)
    {
        // custom implementation depending on specifications
    }
}
```

### Need configuration per container id
If you support more than one instance with different configuration then you simply add one more value to the `dimensions` 
method.

```php
use Interop\Config\RequiresConfig;

class MyAwesomeFactory implements RequiresConfig
{
    public function dimensions()
    {
        return ['vendor-package', 'container-id'];
    }
    
    public function canRetrieveOptions($config)
    {
        // custom implementation depending on specifications
    }
    
    public function options($config)
    {
        // custom implementation depending on specifications
    }
}
```

Ok you have now a factory which says that the factory supports a configuration and you have a PHP file which contains
the configuration as a PHP array, but how is the configuration used?

Depending on the implemented interfaces above our configuration PHP file looks like that:

```php
// interop config example
return [
    // vendor/package name
    'vendor-package' => [
        // container id
        'container-id' => [
            // some options ...
        ],
    ],
];
```

As you can see that you have to implement the functionality of `canRetrieveOptions()` and `options()` method. Good news, 
this is not necessary. See `ConfigurationTrait`. 

## `ConfigurationTrait`
The `ConfigurationTrait` is a concrete implementation of the `RequiresConfig` interface and has full support of 
`ProvidesDefaultOptions` and `RequiresMandatoryOptions` interfaces. It's a 
[PHP Trait](http://php.net/manual/en/language.oop5.traits.php "PHP Trait Documentation") so you can extend your factory
from a class.

Your factory looks now like that:

```php
use Interop\Config\RequiresConfig;
use Interop\Config\ConfigurationTrait;

class MyAwesomeFactory implements RequiresConfig
{
    use ConfigurationTrait;
    
    public function dimensions()
    {
        return ['vendor-package', 'container-id'];
    }
}
```

Now you have all the ingredients to create multiple different instances depending on configuration.
 
## Create an instance
Factories are often implemented as a [callable](http://php.net/manual/en/language.oop5.magic.php#object.invoke "PHP __invoke() Documentation"). 
This means that your factory instance can be called like a function. You can also use a `create` method or something else.

The factory gets a `ContainerInterface` ([Container PSR](https://github.com/php-fig/fig-standards/blob/master/proposed/container-meta.md)) 
provided to retrieve the configuration. 

> Note that the configuration above is injected as `$config` in `options()`

```php
use Interop\Config\RequiresConfig;
use Interop\Config\ConfigurationTrait;
use Interop\Container\ContainerInterface;

class MyAwesomeFactory implements RequiresConfig
{
    use ConfigurationTrait;
    
    public function dimensions()
    {
        return ['vendor-package', 'container-id'];
    }
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for vendor-package.container-id
        // method options() is implemented in ConfigurationTrait
        $options = $this->options($container->get('config'));
        
        return new Awesome($options);
    }
}
```
The `ConfigurationTrait` does the job to check and retrieve options depending on implemented interfaces. *Nice, but what is
if I have mandatory options?* See `RequiresMandatoryOptions` interface.

## `RequiresMandatoryOptions` interface
The `RequiresConfig::options()` interface specification says that it MUST support mandatory options check. Let's say that we need
params for a db connection. Our config *should* looks like that:

```php
// interop config example
return [
    // vendor/package name
    'vendor-package' => [
        // container id
        'container-id' => [
            'params' => [
                'user'     => 'username',
                'password' => 'password',
                'dbname'   => 'database',
            ],
        ],
    ],
];
```

Remember our factory sentence. *My factory requires a configuration and requires a container id along with mandatory options.*.
The `ConfigurationTrait` ensures that these options are available, otherwise an exception is thrown. This is great, because
the developer gets an exact exception message with what is wrong. This is useful for developers who use your factory the first time.

```php
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\ConfigurationTrait;
use Interop\Container\ContainerInterface;

class MyAwesomeFactory implements RequiresConfig, RequiresMandatoryOptions
{
    use ConfigurationTrait;
    
    public function dimensions()
    {
        return ['vendor-package', 'container-id'];
    }
    
    public function mandatoryOptions()
    {
        return ['params' => ['user', 'password', 'dbname']];
    }
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for myvendor.mypackage.mycontainerid
        // method options() is implemented in ConfigurationTrait
        // an exception is raised when a mandatory option is missing
        $options = $this->options($container->get('config'));
        
        return new Awesome($options);
    }
}
```

*Hey, the database port and host is missing.* That's right, but the default value of the port is *3306* and the host is 
*localhost*. It makes no sense to set it in the configuration. *So I make the database port/host not configurable?* No, you 
use the `ProvidesDefaultOptions` interface.
 
## `ProvidesDefaultOptions` interface
The `ProvidesDefaultOptions` interface defines default options for your instance. These options are merged with the provided 
options. 

Remember: *My factory requires configuration, requires a container id along with mandatory options and it provides default options.*

```php
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\ConfigurationTrait;
use Interop\Container\ContainerInterface;

class MyAwesomeFactory implements RequiresConfig, RequiresMandatoryOptions, ProvidesDefaultOptions
{
    use ConfigurationTrait;
    
    public function dimensions()
    {
        return ['vendor-package', 'container-id'];
    }
    
    public function mandatoryOptions()
    {
        return ['params' => ['user', 'password', 'dbname']];
    }
    
    public function defaultOptions()
    {
        return [
            'params' => [
                'host' => 'localhost',
                'port' => '3306',
            ],
        ];
    }
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for vendor-package.container-id
        // method options() is implemented in ConfigurationTrait
        // an exception is raised when a mandatory option is missing
        // if host/port is missing, default options will be used
        $options = $this->options($container->get('config'));
        
        return new Awesome($options);
    }
}
```

Now you have a bullet proof factory class which throws meaningful exceptions if something goes wrong. *This is cool, but
I don't want to use exceptions.* No problem, see next.

## Avoid exceptions
The `RequiresConfig` interface provides a method `canRetrieveOptions()`. This method checks if options are available depending on 
implemented interfaces and checks that the retrieved options are an array or have implemented `\ArrayAccess`.

You can call this function and if it returns false, you can use the default options.


```php
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\ConfigurationTrait;
use Interop\Container\ContainerInterface;

class MyAwesomeFactory implements RequiresConfig, RequiresMandatoryOptions, ProvidesDefaultOptions
{
    use ConfigurationTrait;
    
    // other functions see above
    
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')
        
        $options = [];
        
        if ($this->canRetrieveOptions($config)) {
            // get options for vendor-package.container-id
            // method options() is implemented in ConfigurationTrait
            // if host/port is missing, default options will be used
            $options = $this->options($config);
        } elseif ($this instanceof ProvidesDefaultOptions) {
            $options = $this->defaultOptions();
        }
        
        return new Awesome($options);
    }
}
```

*Nice, is there a one-liner?* Of course. You can use the `optionsWithFallback()` method. This function is not a part
of the specification but is implemented in `ConfigurationTrait` to reduce some boilerplate code.

```php
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\ConfigurationTrait;
use Interop\Container\ContainerInterface;

class MyAwesomeFactory implements RequiresConfig, RequiresMandatoryOptions, ProvidesDefaultOptions
{
    use ConfigurationTrait;
    
    // other functions see above
    
    public function __invoke(ContainerInterface $container)
    {
        // get options for vendor-package.container-id
        // method options() is implemented in ConfigurationTrait
        // if configuration is not available, default options will be used
        $options = $this->optionsWithFallback($container->get('config'));
        
        return new Awesome($options);
    }
}
```

*Using `optionsWithFallback()` method and the `RequiresMandatoryOptions` is ambiguous or?* Yes, so it's up to you to implement
the interfaces in a sense order.

Take a look at the examples section for more use cases. `interop-config` is universally applicable.
