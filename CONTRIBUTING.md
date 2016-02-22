# Contributing

Visit [github.com/sandrokeil/interop-config/](https://github.com/sandrokeil/interop-config/ "Project Website") for the project website.

## Resources

If you wish to contribute to interop-config, please be sure to read to the following resources:

 -  Coding Standards: [PSR-0/1/2/4](https://github.com/php-fig/fig-standards/tree/master/accepted)
 -  Git Guide: [README-GIT.md](README-GIT.md)

If you are working on new features, or refactoring an existing
component, please create a proposal. You can do this in on the RFC's
page, [https://github.com/sandrokeil/interop-config/wiki/RFCs](https://github.com/sandrokeil/interop-config/wiki/RFCs).

## Running tests

To run tests:

- Make sure you have execute `composer install`
- Be sure you are in the root directory
- Execute PHPUnit

  ```sh
  $ ./vendor/bin/phpunit
  ```

You can turn on conditional tests with the TestConfig.php file.
To do so:

 -  Enter the `test/` subdirectory.
 -  Copy `TestConfig.php.dist` file to `TestConfig.php`
 -  Edit `TestConfig.php` to enable any specific functionality you want to test, as well as to provide test values to
    utilize.

## Running benchmarks

To run benchmarks execute *phpbench*

  ```sh
  $ ./vendor/bin/phpbench run --report=aggregate
  ```
