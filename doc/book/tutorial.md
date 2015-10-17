# Tutorial

## Overview

In this short tutorial we will implement a simple example of interop-config. This example tries to give you a better 
understanding of what interop-config is and why you should use it.
 
 
## Components

To implement interopt-config we need a few components to glue it together.

* Configuration
    * This file return an array with the configuration of our application 
* ServiceLocator
    * This class holds the root configuration of our application
    * This class holds some plugin manager classes (invokables, factories, ...) 
* ExampleFactory
    * This class represent the factory for a simple component and implements some interfaces from interop-config
    
## Folders

We use the following simple folder structure for the example application:

* application_root
    * config
        * main.php
    * library
        * ServiceLocator.php
        * SomeVendorName
            * Exception
                * RuntimeException.php
            * MyComponent
                * MyComponentFactory.php
                * MyFirstComponent.php
                * MySecondComponent.php
    * public
        * index.php

# Install composer and interop-config
 
Since we have composer it is really easy to manage our php packages. First of all we need composer run. For that go to your `application_root` directory and setup a composer.json file with the following listing.

```json
{
  "require": {
    "sandrokeil/interop-config": "1.0.x-dev"
  }
}
```

To use composer we need to download the `composer.phar` from [getcomposer.org](https://getcomposer.org) via `curl -sS https://getcomposer.org/installer | php`. 
Next we can get the php packages we setup in the composer.json via `php composer.phar install`

If you need more help look at [getcomposer.org](https://getcomposer.org).


# Setup composer autoloading
  
Put the following code to the public/index.php to get the class auto loading, so we do not have to `include/require` classes before using them.

```php

$loader = require 'vendor/autoload.php';
$loader->add('SomeVendorName\\', dirname(__FILE__).'/../library');

...
```
        
# Setup main configuration

The following code is located in application_root/config/main.php and holds the main configuration `array` of the project.

```php

return [
    'some_vendor_name' => [
        'my_component_configuration' => [
            'driverClass' => 'SomeVendorName\MyComponent\MyFirstComponent',
            'params' => [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ]
        ]
    ]
];
```

# ServiceLocator

The `ServiceLocator` holds the application configuration and some plugin manager classes. 
The `ServiceLocator` is the main entry point if you need to instantiate classes in your application.  

```php

namespace SomeVendorName;

class ServiceLocator
{

    /**
     * Some factory classes
     *
     * @var array
     */
     
    private $factories = array(
        'config' => array(),
        'my_component_factory' => 'SomeVendorName\MyComponent\MyComponentFactory'
        // ...
    );

    public function __construct(array $config)
    {
        $this->factories['config'] = $config;
    }

    /**
     * Get some simple factories
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        // return config simple
        if ($name === 'config' && isset($this->factories['config'])) {
            return $this->factories['config'];
        }

        if (isset($this->factories[$name])) {
            $FactoryClass = new $this->factories[$name]();
            // if your factory implements a factory interface you should check it at this place
            return $FactoryClass->__invoke($this);
        }

    }

    /**
     * Check if factory exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->factories[$name]);
    }

}
```

# Runtime Exception

To throw useful exceptions we have a `RuntimeException` in our namespace. There are many more exception types, but for 
this simple example we satisfied by the `RuntimeException`. The following code is located in 
application_root/library/SomeVendorName/Exception/RuntimeException.php.

```php

namespace SomeVendorName\Exception;

class RuntimeException extends \Exception {

}

```

# The component factory class

The factory class implements `OptainOptions` and uses the `ConfigurationTrait`. The factory class creates components 
with the configuration we explicitly setup before. Every component created by this factory will get the same configuration parameters.

```php

namespace SomeVendorName\MyComponent;

use SomeVendorName\ServiceLocator;
use SomeVendorName\Exception\RuntimeException;

use Interop\Config\ObtainsOptions;
use Interop\Config\ConfigurationTrait;

class MyComponentFactory implements ObtainsOptions
{

    use ConfigurationTrait;

    public function vendorName()
    {
        return 'some_vendor_name';
    }

    public function componentName()
    {
        return 'my_component_configuration';
    }

    public function __invoke(ServiceLocator $ServiceLocator)
    {

        $options = $this->options($ServiceLocator->get('config'));

        // check if mandatory options are available or use \Interop\Config\HasMandatoryOptions, see below
        if (empty($options['driverClass'])) {
            throw new RuntimeException(
                sprintf(
                    'Driver class was not set for configuration %s.%s.%s',
                    'doctrine',
                    'connection',
                    'orm_default'
                )
            );
        }

        if (empty($options['params'])) {
            throw new RuntimeException(
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

        return new $driverClass($params);
    }
}
```

# The first component class

Finally we have the component class where we need the configuration.

```php

namespace SomeVendorName\MyComponent;

use SomeVendorName\Exception\RuntimeException;

class MyFirstComponent
{
    private $config = array();

    public function __construct($config = array()){
        $this->config = $config;

        // use config params in here
        var_dump($config);

    }
}
```

# Run the example application

To run the example application we just need the following few lines in the public/index.php:

```php

$loader = require 'vendor/autoload.php';
$loader->add('SomeVendorName\\', __DIR__.'/library');

$ServiceLocator = new SomeVendorName\ServiceLocator(include dirname('__FILE__')."/../config/main.php");
$MyComponentFactory = $ServiceLocator->get('my_component_factory');

...
```

If you see some output different from fatal error you have successfully implement interop-config.







