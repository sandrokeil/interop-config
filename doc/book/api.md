# interop-config API

This file describes the classes of this package.

## HasConfig Interface

The `HasConfig` interface exposes two methods: `vendorName` and `packageName`.

### vendorName()
```php
    public function vendorName() : string
```

The `vendorName` method has no parameters and MUST return a string.

### packageName()
```php
    public function packageName() : string
```

The `packageName` method has no parameters and MUST return a string.

## HasContainerId Interface
The `HasContainerId` interface exposes one method: `containerId`

### containerId()
```php
    public function containerId() : string
```

The `containerId` method has no parameters and MUST return a string.

## HasMandatoryOptions Interface
The `HasMandatoryOptions` interface exposes one method: `mandatoryOptions`

### mandatoryOptions()
```php
    public function mandatoryOptions() : string[]
```
The `mandatoryOptions` method has no parameters and MUST return an array of strings which represents the list of mandatory 
options. This array can have a multiple depth.

## ObtainsOptions Interface
The `ObtainsOptions` interface exposes two method: `canRetrieveOptions` and `options`

### canRetrieveOptions()
```php
    public function canRetrieveOptions($config) : bool
```

### options()
```php
    public function options($config) : []
```
The `options` method takes one mandatory parameter: a configuration array. It MUST be an array or an object which implements the 
`ArrayAccess` interface. A call to `options` returns the configuration depending on the implemented interfaces of the 
class or throws an exception if the parameter is invalid or if the configuration is missing or if a mandatory option is missing.

If the `HasDefaultOptions` interface is implemented, these options must be overriden by the provided config.

The `HasContainerId` interface is optional but MUST be supported.

#### Exceptions
Exceptions directly thrown by the `options` method MUST implement the `Interop\Config\ExceptionExceptionInterface`.

If the configuration parameter is not an array or not an object which implementes the `ArrayAccess` interface the method 
SHOULD throw a `Interop\Config\ExceptionInvalidArgumentException`.

If the key which is returned from `vendorName` is not set in the configuration parameter the method SHOULD throw a 
`Interop\Config\Exception\OutOfBoundsException`.

If the key which is returned from `packageName` is not set under the key of `vendorName` in the configuration parameter 
the method SHOULD throw a `Interop\Config\Exception\OptionNotFoundException`.

If the class implements the `HasContainerId` interface and if the key which is returned from `containerId` is not set
under the key of `packageName` in the configuration parameter the method SHOULD throw a 
`Interop\Config\Exception\OptionNotFoundException`.

If the class implements the `HasMandatoryOptions` interface and if a mandatory option from `mandatoryOptions` is not set 
in the options array which was retrieved from the configuration parameter before, the method SHOULD throw a 
`Interop\Config\Exception\MandatoryOptionNotFoundException`.

If the retrieved options are not of type array or \ArrayAccess the method SHOULD throw a `Interop\Config\Exception\UnexpectedValueException`.

## OptionalOptions Interface
The `OptionalOptions` interface exposes one method: `optionalOptions`

### optionalOptions()
```php
    public function optionalOptions() : []
```
The `optionalOptions` has no parameters and MUST return an array of strings which represents the list of optional options. 
This array can have a multiple depth.

## DefaultOptions Interface
The `DefaultOptions` interface exposes one method: `defaultOptions`

### defaultOptions()
```php
    public function defaultOptions() : []
```
The `defaultOptions` method has no parameters and MUST return an key value array where the key is the option name and 
the value is the default value for this option. This array can have a multiple depth.
The return value MUST be compatible with the PHP function `array_replace_recursive`.
