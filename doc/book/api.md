# API

This file describes the classes of this package. All interfaces can be combined. Don't panic, the interfaces are
quite easy and the `ConfigurationTrait`, which is a concrete implementation, has full support of those interfaces. You
need only one interface called `RequiresConfig` to start and then you can implement the others if they are needed.

* `RequiresConfig` Interface
* `RequiresConfigId` Interface
* `RequiresMandatoryOptions` Interface
* `ProvidesDefaultOptions` Interface

## RequiresConfig Interface

The `RequiresConfig` interface exposes three methods: `dimensions`, `canRetrieveOptions` and `options`.

### dimensions()
```php
    public function dimensions() : iterable
```

The `dimensions` method has no parameters and MUST return an array. The values (used as key names) of the array are used
as the depth of the configuration to retrieve options. Two values means a configuration depth of two. An empty array is
valid.

### canRetrieveOptions()
```php
    public function canRetrieveOptions($config) : bool
```
Checks if options are available depending on provided dimensions and checks that the retrieved options are an array or
have implemented \ArrayAccess.

### options()
```php
    public function options($config) : []
``` 
The `options` method takes one mandatory parameter: a configuration array. It MUST be an array or an object which implements the
`ArrayAccess` interface. A call to `options` returns the configuration depending on provided dimensions of the
class or throws an exception if the parameter is invalid or if the configuration is missing or if a mandatory option is missing.

If the `ProvidesDefaultOptions` interface is implemented, these options MUST be overridden by the provided config.

#### Exceptions
Exceptions directly thrown by the `options` method MUST implement the `Interop\Config\Exception\ExceptionInterface`.

If the configuration parameter is not an array or not an object which implements the `ArrayAccess` interface the method
SHOULD throw a `Interop\Config\Exception\InvalidArgumentException`. 
 
If a key which is returned from `dimensions` is not set under the previous dimensions key in the configuration parameter,
the method SHOULD throw a `Interop\Config\Exception\OptionNotFoundException`.

If a value from the configuration based on dimensions is not an array or an object which has `\ArrayAccess` implemented,
the method SHOULD throw a `Interop\Config\Exception\UnexpectedValueException`.

If the class implements the `RequiresMandatoryOptions` interface and if a mandatory option from `mandatoryOptions` is not set
in the options array which was retrieved from the configuration parameter before, the method SHOULD throw a
`Interop\Config\Exception\MandatoryOptionNotFoundException`.

If the retrieved options are not of type array or \ArrayAccess the method SHOULD throw a `Interop\Config\Exception\UnexpectedValueException`.

## RequiresMandatoryOptions Interface
The `RequiresMandatoryOptions` interface exposes one method: `mandatoryOptions`

### mandatoryOptions()
```php
    public function mandatoryOptions() : iterable
```
The `mandatoryOptions` method has no parameters and MUST return an array of strings which represents the list of mandatory
options. This array can have a multiple depth.

## ProvidesDefaultOptions Interface
The `DefaultOptions` interface exposes one method: `defaultOptions`

### defaultOptions()
```php
    public function defaultOptions() : iterable
```
The `defaultOptions` method has no parameters and MUST return an key-value array where the key is the option name and
the value is the default value for this option. This array can have a multiple depth.
The return value MUST be compatible with the PHP function `array_replace_recursive`.

## RequiresConfigId
The `RequiresConfigId` is only a marker interface and has no methods. It marks the factory that multiple instances are
supported. The `ConfigurationTrait` has an optional parameter `$configId` implemented for the methods of `RequiresConfig`.
So it is full supported.

## ConfigurationTrait
The `ConfigurationTrait` implements the functions of `RequiresConfig` interface and has support for
`ProvidesDefaultOptions`, `RequiresMandatoryOptions` and `RequiresConfigId` interfaces if the the class has they implemented.

Additional it has one more method `optionsWithFallback` to reduce boilerplate code.

### optionsWithFallback()
```php
    public function optionsWithFallback($config) : []
```
Checks if options can be retrieved from config and if not, default options (`ProvidesDefaultOptions` interface) or an empty array will be returned.
