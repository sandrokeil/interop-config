# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.2.0 (2020-09-05)

### Added

* PHP 8 support

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 2.1.0 (2017-02-14)

### Added

* [#36](https://github.com/sandrokeil/interop-config/issues/36): Create console tools to generate/display config based on factories. Read more in the [docs](https://sandrokeil.github.io/interop-config/reference/console-tools.html).
* Composer suggestion of `psr/container`

### Deprecated

* Nothing

### Removed

* Composer suggestion of `container-interop/container-interop`

### Fixed

* Nothing


## 2.0.1 (2016-12-09)
This release contains **no** BC break.

### Added

* More test cases for iterable return type and `\Iterator` objects

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* [#34](https://github.com/sandrokeil/interop-config/issue/34): Inconsistent return type in `defaultOptions()`
  * `defaultOptions()` method return type is `iterable` but `array` is still valid


## 2.0.0 (2016-12-06)
To upgrade from version 1.x to version 2.x you have to add the PHP scalar types of the interfaces to your implemented 
factory methods.

### Added

* [#33](https://github.com/sandrokeil/interop-config/pull/33): PHP 7.1 language features (return types, scalar type hints)
  * `dimensions()` method return type is `iterable`
  * `canRetrieveOptions()` method return type is `bool`
  * `mandatoryOptions()` method return type is `iterable`
  * `defaultOptions()` method return type is `array`
* Minor performance improvements
* More test cases

### Deprecated

* Nothing

### Removed

* PHP < 7.1 support

### Fixed

* Nothing


## 1.0.0 (2016-03-05)

> This release contains BC breaks, but upgrade path is simple.

### Added

* [#26](https://github.com/sandrokeil/interop-config/pull/26): `dimensions()` method to `RequiresConfig` to make configuration depth flexible

### Deprecated

* Nothing

### Removed

* [#26](https://github.com/sandrokeil/interop-config/pull/26): `vendorName()` and `packageName()` method from `RequiresConfig`, replaced by `dimensions()` method
    * It's recommended to remove the methods and use the values directly in `dimensions()` to increase performance

    ```php
    public function dimensions()
    {
        return [$this->vendorName(), $this->packageName()];
    }
    ```

* [#26](https://github.com/sandrokeil/interop-config/pull/26): `RequiresContainerId` interface is renamed to `RequiresConfigId` 
    * use the container id as a second argument by `options()` method.

### Fixed

* [#28](https://github.com/sandrokeil/interop-config/pull/28): Throws exception if dimensions are set but default options are available and no mandatory options configured

## 0.3.1 (2015-10-21)

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Fixed *Illegal offset type in isset or empty* if options are empty and recursive mandatory options are used

## 0.3.0 (2015-10-18)

### Added

* [#9](https://github.com/sandrokeil/interop-config/issues/9): Introducing ProvidesDefaultOptions interface
* [#13](https://github.com/sandrokeil/interop-config/issues/13): Support for recursive mandatory options check
* `canRetrieveOptions()` to `ConfigurationTrait` to perform the options check without throwing an exception 
* `optionsWithFallback()` to `ConfigurationTrait` which uses default options if config can not be retrieved
* OptionNotFoundException and MandatoryOptionNotFoundException extends OutOfBoundsException instead of RuntimeException
* Check if retrieved options are an array or an instance of ArrayAccess
* Benchmark suite
* Updated documentation

### Deprecated

* Nothing

### Removed

* `HasConfig` interface, was renamed to `RequiresConfig`
* `HasContainer` interface, was renamed to `RequiresContainerId`
* `HasMandatoryOptions` interface, was renamed to `RequiresMandatoryOptions`
* `HasDefaultOptions` interface, was renamed to `ProvidesDefaultOptions`
* `ObtainsOptions` interface, was merged in `RequiresConfig`
* `OptionalOptions` interface, can be achieved via `ProvidesDefaultOptions`

### Fixed

* fixed wrong function name in documentation

## 0.2.0 (2015-09-20)

### Added

* [#5](https://github.com/sandrokeil/interop-config/issues/5): replaced `componentName` function with `packageName` ([PSR-4 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-meta.md#package-oriented-autoloading))

### Deprecated

* Nothing

### Removed

* [#5](https://github.com/sandrokeil/interop-config/issues/5): `componentName` function from `HasConfig` interface (BC break)

### Fixed

* Nothing

## 0.1.0 (2015-09-05)

### Added
* Initial release
* Added interfaces
* [#2](https://github.com/sandrokeil/interop-config/issues/2): Added trait implementation

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing
