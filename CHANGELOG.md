# Interop Config CHANGELOG

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.3.0 TBD

### Added

* [#8](https://github.com/sandrokeil/interop-config/issues/8): Introducing HasOptionalOptions Interface
* [#9](https://github.com/sandrokeil/interop-config/issues/9): Introducing HasDefaultOptions Interface
* [#13](https://github.com/sandrokeil/interop-config/issues/13): Support for recursive mandatory options check
* `canRetrieveOptions()` to `ConfigurationTrait` to perform the options check without throwing an exception 
* OptionNotFoundException and MandatoryOptionNotFoundException extends OutOfBoundsException instead of RuntimeException
* Check if retrieved options are an array or an instance of ArrayAccess
* Benchmark suite
* Updated documentation

### Deprecated

* Nothing

### Removed

* Nothing

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
